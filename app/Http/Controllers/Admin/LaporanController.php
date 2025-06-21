<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan; // Model untuk retur, sesuai dengan kode Anda
use App\Models\Barang;
use App\Models\Penjualan; // Model untuk penjualan
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response; // Tambahkan ini untuk Response::download

class LaporanController extends Controller
{
    /**
     * Menampilkan dashboard laporan penjualan dan retur.
     * Termasuk ringkasan, data untuk grafik, dan daftar detail retur.
     */
    public function index(Request $request)
    {
        // Mendapatkan rentang tanggal dari request.
        // Default: 30 hari terakhir jika tidak ada input.
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();

        // Pastikan endDate mencakup seluruh hari yang dipilih
        // Ini penting agar data pada tanggal endDate juga ikut terhitung.
        $endDate = $endDate->endOfDay();

        // 1. Menghitung Total Penjualan
        // Mengambil total harga dari semua transaksi penjualan dalam rentang tanggal.
        $totalPenjualan = Penjualan::whereBetween('waktu_terjual', [$startDate, $endDate])
                                 ->sum('hargaTotal');

        // 2. Menghitung Total Nilai Retur
        // Bergabung dengan tabel 'barangs' untuk mendapatkan harga per barang
        // dan menghitung total nilai retur (jumlah_retur * harga_barang).
        $totalNilaiRetur = Laporan::whereBetween('tanggal_retur', [$startDate, $endDate])
                                 ->join('barangs', 'laporans.id_barang', '=', 'barangs.id')
                                 ->selectRaw('SUM(laporans.jumlah_retur * barangs.harga) as total_retur_value')
                                 ->first()
                                 ->total_retur_value ?? 0; // Pastikan nilai default 0 jika tidak ada retur

        // 3. Menghitung Hasil Murni Penjualan
        $hasilMurniPenjualan = $totalPenjualan - $totalNilaiRetur;

        // 4. Mengambil Daftar Laporan Retur (untuk ditampilkan dalam tabel detail)
        // Eager load relasi 'barang' dan 'penjualan' untuk menghindari N+1 query problem.
        $laporansRetur = Laporan::with('barang', 'penjualan')
                                 ->whereBetween('tanggal_retur', [$startDate, $endDate])
                                 ->orderBy('tanggal_retur', 'desc') // Urutkan berdasarkan tanggal retur terbaru
                                 ->paginate(10); // Tetap gunakan paginasi untuk tabel

        // 5. Mengambil Data untuk Diagram Lingkaran (Penjualan Berdasarkan Produk)
        // Bergabung dengan tabel 'barangs' untuk mendapatkan nama produk
        // dan menjumlahkan total terjual per produk.
        $penjualanByProduk = Penjualan::whereBetween('waktu_terjual', [$startDate, $endDate])
                                       ->join('barangs', 'penjualans.id_barang', '=', 'barangs.id')
                                       ->selectRaw('barangs.nama_produk, SUM(penjualans.jumlahTerjual) as total_terjual')
                                       ->groupBy('barangs.nama_produk')
                                       ->get();

        // Mengembalikan view dengan semua data yang diperlukan
        return view('admin.laporans.index', compact(
            'totalPenjualan',
            'totalNilaiRetur',
            'hasilMurniPenjualan',
            'laporansRetur',
            'penjualanByProduk',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Menampilkan form untuk membuat laporan retur baru.
     * Memuat semua penjualan untuk dropdown pilihan.
     */
    public function create()
    {
        // Ambil semua penjualan dengan eager loading relasi 'barang'
        $penjualans = Penjualan::with('barang')->get();
        return view('admin.laporans.create', compact('penjualans'));
    }

    /**
     * Menyimpan laporan retur baru ke database.
     * Melakukan validasi input dan memperbarui stok barang terkait.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'id_penjualan' => 'required|exists:penjualans,id',
            'id_barang' => 'required|exists:barangs,id',
            'tanggal_retur' => 'required|date',
            'jumlah_retur' => 'required|integer|min:1',
        ]);

        // Gunakan transaksi database untuk memastikan konsistensi
        DB::transaction(function () use ($request) {
            $barang = Barang::find($request->id_barang);

            if (!$barang) {
                // Ini seharusnya tidak terjadi karena validasi 'exists'
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'id_barang' => 'Barang tidak ditemukan.'
                ]);
            }

            // Hitung nilai retur
            $nilaiRetur = $request->jumlah_retur * $barang->harga; // Asumsi 'harga' adalah harga jual di model Barang

            // *** MODIFIKASI: Perbarui stok_masuk dan jumlah_stok di tabel barangs ***
            $barang->stok_masuk += $request->jumlah_retur; // Tambahkan jumlah retur ke stok_masuk
            $barang->jumlah_stok = $barang->stok_awal + $barang->stok_masuk - $barang->stok_keluar; // Hitung ulang total stok
            // *** END MODIFIKASI ***
            $barang->status = 'Masuk'; // Asumsi ini adalah kolom di tabel barang Anda
            $barang->keterangan = 'Retur'; // Asumsi ini adalah kolom di tabel barang Anda
            $barang->updated_at = now();

            // Simpan perubahan pada model Barang
            $barang->save();

            // Buat record laporan retur
            Laporan::create([
                'id_penjualan' => $request->id_penjualan,
                'id_barang' => $request->id_barang,
                'tanggal_retur' => $request->tanggal_retur,
                'jumlah_retur' => $request->jumlah_retur,
                'nilai_retur' => $nilaiRetur, // Simpan nilai retur ke DB
            ]);
        });

        // Redirect ke halaman index laporan dengan pesan sukses
        return redirect()->route('admin.laporans.index')->with('success', 'Laporan retur berhasil ditambahkan, stok barang diperbarui.');
    }

    /**
     * Menampilkan detail satu laporan retur.
     */
    public function show(Laporan $laporan) // Menggunakan Route Model Binding
    {
        // Eager load relasi barang dan penjualan untuk menghindari N+1 query problem
        $laporan->load('barang', 'penjualan');

        return view('admin.laporans.show', compact('laporan'));
    }

    /**
     * Menampilkan form untuk mengedit laporan retur yang sudah ada.
     * Memuat laporan berdasarkan ID dan semua penjualan.
     */
    public function edit($id)
    {
        // Temukan laporan berdasarkan ID, jika tidak ada akan melempar 404
        $laporan = Laporan::findOrFail($id);

        // Ambil semua penjualan dengan eager loading relasi 'barang'
        $penjualans = Penjualan::with('barang')->get();

        return view('admin.laporans.edit', compact('laporan', 'penjualans'));
    }

    /**
     * Memperbarui laporan retur di database.
     * Melakukan validasi input dan menyimpan perubahan, serta menyesuaikan stok barang.
     */
    public function update(Request $request, $id)
    {
        // Temukan laporan yang akan diperbarui
        $laporan = Laporan::findOrFail($id);

        // Simpan jumlah retur lama dan id_barang lama sebelum update untuk perhitungan stok
        $oldJumlahRetur = $laporan->jumlah_retur;
        $oldIdBarang = $laporan->id_barang;

        // Validasi input dari form
        $request->validate([
            'id_penjualan' => 'required|exists:penjualans,id',
            'id_barang' => 'required|exists:barangs,id',
            'tanggal_retur' => 'required|date',
            'jumlah_retur' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $laporan, $oldJumlahRetur, $oldIdBarang) {
            $newIdBarang = $request->id_barang;
            $newJumlahRetur = $request->jumlah_retur;

            // *** MODIFIKASI: Menyesuaikan stok_masuk dan jumlah_stok di tabel barangs ***
            // Skenario 1: id_barang berubah
            if ($oldIdBarang != $newIdBarang) {
                // 1a. Kurangi stok_masuk dan jumlah_stok dari barang lama
                $oldBarang = Barang::find($oldIdBarang);
                if ($oldBarang) {
                    $oldBarang->stok_masuk -= $oldJumlahRetur;
                    $oldBarang->jumlah_stok = $oldBarang->stok_awal + $oldBarang->stok_masuk - $oldBarang->stok_keluar;
                    $oldBarang->save();
                }

                // 1b. Tambahkan stok_masuk dan jumlah_stok ke barang baru
                $newBarang = Barang::find($newIdBarang);
                if (!$newBarang) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'id_barang' => 'Barang baru tidak ditemukan.'
                    ]);
                }
                $newBarang->stok_masuk += $newJumlahRetur;
                $newBarang->jumlah_stok = $newBarang->stok_awal + $newBarang->stok_masuk - $newBarang->stok_keluar;
                $newBarang->save();

            } else { // Skenario 2: id_barang TIDAK berubah, sesuaikan stok pada barang yang sama
                $barang = Barang::find($newIdBarang);
                if (!$barang) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'id_barang' => 'Barang tidak ditemukan.'
                    ]);
                }
                // Hitung selisih jumlah retur: (jumlah_retur_baru - jumlah_retur_lama)
                $stokDifference = $newJumlahRetur - $oldJumlahRetur;
                $barang->stok_masuk += $stokDifference; // Sesuaikan stok_masuk
                $barang->jumlah_stok = $barang->stok_awal + $barang->stok_masuk - $barang->stok_keluar; // Hitung ulang total stok
                $barang->save();
            }
            // *** END MODIFIKASI ***

            // Hitung nilai retur baru
            $barangCurrent = Barang::find($newIdBarang); // Dapatkan barang terbaru setelah perubahan stok
            $nilaiRetur = $barangCurrent ? $newJumlahRetur * $barangCurrent->harga : 0; // Asumsi 'harga' di Barang

            // Update laporan retur
            $laporan->update([
                'id_penjualan' => $request->id_penjualan,
                'id_barang' => $newIdBarang,
                'tanggal_retur' => $request->tanggal_retur,
                'jumlah_retur' => $newJumlahRetur,
                'nilai_retur' => $nilaiRetur, // Simpan nilai retur terbaru
            ]);
        });

        // Redirect ke halaman index laporan dengan pesan sukses
        return redirect()->route('admin.laporans.index')->with('success', 'Laporan retur berhasil diperbarui dan stok disesuaikan.');
    }

    /**
     * Menghapus laporan retur dari database.
     * Mengembalikan stok barang seperti semula.
     */
    public function destroy($id)
    {
        // Temukan laporan yang akan dihapus
        $laporan = Laporan::findOrFail($id);

        DB::transaction(function () use ($laporan) {
            $barang = Barang::find($laporan->id_barang);

            // *** MODIFIKASI: Mengembalikan stok_masuk dan jumlah_stok di tabel barangs ***
            if ($barang) {
                $barang->stok_masuk -= $laporan->jumlah_retur; // Kurangi stok_masuk
                $barang->jumlah_stok = $barang->stok_awal + $barang->stok_masuk - $barang->stok_keluar; // Hitung ulang total stok
                $barang->save();
            }
            // *** END MODIFIKASI ***

            // Hapus laporan retur
            $laporan->delete();
        });

        return redirect()->route('admin.laporans.index')->with('success', 'Laporan retur berhasil dihapus dan stok dikembalikan.');
    }

    /**
     * Mengambil daftar barang yang terkait dengan penjualan tertentu (melalui AJAX).
     * Digunakan untuk mengisi dropdown barang di form laporan retur.
     */
    public function getBarangByPenjualan($penjualanId)
    {
        // Temukan penjualan berdasarkan ID dan eager load relasi 'barang'
        $penjualan = Penjualan::with('barang')->find($penjualanId);

        // Jika penjualan tidak ditemukan, atau tidak ada barang terkait, kembalikan array kosong
        if (!$penjualan || !$penjualan->barang) {
            return response()->json([]);
        }

        // Karena model Penjualan Anda mewakili satu item barang per baris,
        // kita membuat array yang berisi satu objek barang ini.
        // Ini agar formatnya tetap array untuk JavaScript di frontend yang menggunakan forEach.
        $barangs = [
            [
                'id' => $penjualan->barang->id,
                'nama_produk' => $penjualan->barang->nama_produk,
            ]
        ];

        return response()->json($barangs);
    }

    /**
     * Export laporan PENJUALAN ke Excel.
     * Mengambil data penjualan beserta informasi retur terkait.
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();

        // Pastikan endDate mencakup seluruh hari yang dipilih
        $endDate = $endDate->endOfDay();

        // Subquery untuk menghitung total jumlah retur per kombinasi id_penjualan dan id_barang
        $returnsSubquery = Laporan::select('id_penjualan', 'id_barang', DB::raw('SUM(jumlah_retur) as total_retur_quantity'))
            ->groupBy('id_penjualan', 'id_barang');

        // Query utama untuk data penjualan, di-left join dengan subquery retur
        $salesData = Penjualan::select(
                'penjualans.id',
                'barangs.nama_produk',
                'penjualans.jumlahTerjual', // Kuantitas asli yang terjual dari tabel penjualan
                'barangs.harga',             // Harga satuan barang (asumsi 'harga' di Barang adalah harga jual)
                'penjualans.waktu_terjual',
                DB::raw('COALESCE(returns.total_retur_quantity, 0) as jumlah_retur_item') // Jumlah retur dari subquery
            )
            ->join('barangs', 'penjualans.id_barang', '=', 'barangs.id')
            ->leftJoinSub($returnsSubquery, 'returns', function($join) {
                // Gabungkan data penjualan dengan hasil agregasi retur
                $join->on('returns.id_penjualan', '=', 'penjualans.id')
                     ->on('returns.id_barang', '=', 'penjualans.id_barang');
            })
            ->whereBetween('penjualans.waktu_terjual', [$startDate, $endDate])
            ->orderBy('penjualans.waktu_terjual', 'asc')
            ->get();

        // Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Penjualan'); // Ganti judul sheet menjadi Laporan Penjualan

        // Header kolom untuk Laporan Penjualan
        $headers = [
            'No.',
            'Nama Barang',
            'Jumlah Terjual (Bruto)', // Kuantitas asli yang terjual
            'Retur (Kuantitas)',      // Kuantitas yang diretur
            'Total Terjual (Bersih)', // Jumlah Terjual - Retur
            'Harga Total (Bersih)',   // (Jumlah Terjual - Retur) * Harga Satuan
            'Waktu Terjual',
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Isi data
        $row = 2;
        foreach ($salesData as $index => $data) {
            $jumlahTerjualBersih = $data->jumlahTerjual - $data->jumlah_retur_item;
            $hargaTotalBersih = $jumlahTerjualBersih * $data->harga; // Menggunakan harga satuan dari barang

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $data->nama_produk);
            $sheet->setCellValue('C' . $row, $data->jumlahTerjual);
            $sheet->setCellValue('D' . $row, $data->jumlah_retur_item);
            $sheet->setCellValue('E' . $row, $jumlahTerjualBersih);
            // Format angka sebagai mata uang (contoh Rupiah, tanpa simbol Rp)
            $sheet->setCellValue('F' . $row, $hargaTotalBersih);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0'); // Contoh format angka dengan ribuan separator
            $sheet->setCellValue('G' . $row, Carbon::parse($data->waktu_terjual)->format('d F Y H:i')); // Format tanggal waktu

            $row++;
        }

        // Auto-size kolom
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Siapkan untuk download
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Penjualan_' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName); // Buat file sementara
        $writer->save($tempFile);

        return Response::download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ])->deleteFileAfterSend(true); // Hapus file sementara setelah dikirim
    }
}