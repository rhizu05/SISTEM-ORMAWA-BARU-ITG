<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileDataController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'nama_ketua' => 'nullable|string|max:255',
            'nama_sekretaris' => 'nullable|string|max:255',
            'nama_bendahara' => 'nullable|string|max:255',
            'foto_profil' => 'nullable|image|max:2048',
            'logo_ormawa' => 'nullable|image|max:2048',
            'ttd_ketua' => 'nullable|image|mimes:png|max:1024',
            'ttd_sekretaris' => 'nullable|image|mimes:png|max:1024',
            'ttd_bendahara' => 'nullable|image|mimes:png|max:1024',
        ], [
            'ttd_ketua.mimes' => 'Tanda tangan harus berupa file PNG (disarankan transparan).',
            'ttd_sekretaris.mimes' => 'Tanda tangan harus berupa file PNG (disarankan transparan).',
            'ttd_bendahara.mimes' => 'Tanda tangan harus berupa file PNG (disarankan transparan).',
        ]);

        $data = $request->only(['alamat', 'telepon', 'nama_ketua', 'nama_sekretaris', 'nama_bendahara']);

        // Handle file uploads
        $files = ['foto_profil', 'logo_ormawa', 'ttd_ketua', 'ttd_sekretaris', 'ttd_bendahara'];
        
        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                // Hapus file lama jika ada
                if ($user->$file && Storage::disk('public')->exists($user->$file)) {
                    Storage::disk('public')->delete($user->$file);
                }
                
                $data[$file] = $request->file($file)->store('profil', 'public');
            }
        }

        $user->update($data);

        return redirect()->back()->with('status', 'profile-data-updated');
    }
}
