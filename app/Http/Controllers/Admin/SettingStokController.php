<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppSetting;

class SettingStokController extends Controller
{
    public function index()
    {
        $settings = AppSetting::where('setting_key', 'like', 'limit_stok_%')->get();
        return view('admin.setting_stok.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->setting_value as $id => $value) {
            AppSetting::where('id', $id)->update([
                'setting_value' => $value,
            ]);
        }

        return redirect()->back()->with('success', 'Limit stok berhasil diperbarui.');
    }
}