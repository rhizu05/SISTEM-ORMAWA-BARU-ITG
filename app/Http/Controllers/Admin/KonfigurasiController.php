<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Konfigurasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KonfigurasiController extends Controller
{
    public function edit()
    {
        $konfigurasis = Konfigurasi::all()->pluck('nilai_konfigurasi', 'nama_konfigurasi')->toArray();
        return view('admin.konfigurasi.edit', compact('konfigurasis'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_aplikasi' => 'required|string|max:255',
            'kop_baris1' => 'nullable|string|max:255',
            'kop_baris2' => 'nullable|string|max:255',
            'kop_baris3' => 'nullable|string|max:255',
            'kop_baris4' => 'nullable|string|max:255',
            'logo_sistem' => 'nullable|image|max:2048',
            'kop_logo' => 'nullable|image|max:2048',
        ]);

        // Text configurations
        $textConfigs = [
            'nama_aplikasi', 'kop_baris1', 'kop_baris2', 'kop_baris3', 'kop_baris4'
        ];

        foreach ($textConfigs as $key) {
            Konfigurasi::updateOrCreate(
                ['nama_konfigurasi' => $key],
                ['nilai_konfigurasi' => $request->input($key)]
            );
        }

        // File configurations
        if ($request->hasFile('logo_sistem')) {
            $path = $request->file('logo_sistem')->store('sistem', 'public');
            Konfigurasi::updateOrCreate(
                ['nama_konfigurasi' => 'logo_sistem'],
                ['nilai_konfigurasi' => $path]
            );
        }

        if ($request->hasFile('kop_logo')) {
            $path = $request->file('kop_logo')->store('sistem', 'public');
            Konfigurasi::updateOrCreate(
                ['nama_konfigurasi' => 'kop_logo'],
                ['nilai_konfigurasi' => $path]
            );
        }

        return redirect()->back()->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }
}
