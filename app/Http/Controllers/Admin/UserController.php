<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select('id', 'name', 'username', 'roles');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('raw_password', function () {
                    return '****';
                })
                ->addColumn('action', function ($user) {
                    return '
                        <a href="' . route('admin.users.edit', $user->id) . '" class="bg-yellow-400 px-2 py-1 rounded">Ubah</a>
                        <form action="' . route('admin.users.destroy', $user->id) . '" method="POST" class="inline">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button onclick="return confirm(\'Yakin ingin hapus?\')" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                        </form>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.users.index');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'roles' => 'required|in:ADMIN,USER',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'roles' => $request->roles,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'roles' => 'required|in:Admin,User',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'roles' => $request->roles,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}