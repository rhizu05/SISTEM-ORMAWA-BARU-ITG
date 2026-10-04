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
            'nim_ketua' => 'nullable|string|max:50',
            'nama_sekretaris' => 'nullable|string|max:255',
            'nim_sekretaris' => 'nullable|string|max:50',
            'nama_bendahara' => 'nullable|string|max:255',
            'nim_bendahara' => 'nullable|string|max:50',
            'foto_profil' => 'nullable|image|max:2048',
            'logo_ormawa' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'alamat', 'telepon',
            'nama_ketua', 'nim_ketua',
            'nama_sekretaris', 'nim_sekretaris',
            'nama_bendahara', 'nim_bendahara',
        ]);

        // Handle file uploads
        $files = ['foto_profil', 'logo_ormawa'];
        
        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                // Hapus file lama jika ada
                if ($user->$file && Storage::disk('public')->exists($user->$file)) {
                    Storage::disk('public')->delete($user->$file);
                }
                
                $data[$file] = $request->file($file)->store('profil', 'public');
            }
        }

        // Jika upload logo_ormawa tetapi belum punya foto_profil, sinkronkan demi kompatibilitas
        if (isset($data['logo_ormawa']) && empty($user->foto_profil) && !isset($data['foto_profil'])) {
            $data['foto_profil'] = $data['logo_ormawa'];
        }

        $user->update($data);

        return redirect()->back()->with('status', 'profile-data-updated');
    }
}
