<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\PeminjamanTempat;
use App\Models\PeminjamanBarang;
use App\Models\User;
use App\Models\WorkflowTransition;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->roles->first()?->name;

        if ($role === 'ormawa') {
            $stats = [
                'total_pengajuan' => Pengajuan::where('user_id', $user->id)->count(),
                'saldo' => $user->saldo,
                'sedang_proses' => Pengajuan::where('user_id', $user->id)->whereHas('state', function($q) {
                    $q->whereNotIn('name', ['draft', 'completed', 'rejected']);
                })->count(),
            ];
            return view('dashboard.ormawa', compact('stats'));
        } 
        
        elseif (in_array($role, ['bem', 'bpm', 'bkh', 'wr3'])) {
            $allowedTransitions = WorkflowTransition::where('required_role', $role)->get();
            $allowedStateIds = $allowedTransitions->pluck('from_state_id')->unique();
            
            $stats = [
                'antrian_verifikasi' => Pengajuan::whereIn('workflow_state_id', $allowedStateIds)->count(),
                'total_disetujui' => Pengajuan::whereHas('histori', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count(),
            ];
            return view('dashboard.verifikator', compact('stats'));
        }
        
        elseif ($role === 'bendahara') {
            $stats = [
                'siap_cair' => Pengajuan::whereHas('state', function($q) {
                    $q->where('name', 'to_treasurer');
                })->count(),
                'total_dicairkan' => \App\Models\Dana::sum('nominal_cair'),
            ];
            return view('dashboard.bendahara', compact('stats'));
        }
        
        elseif (in_array($role, ['sarpras', 'sarpras_barang'])) {
            $stats = [
                'peminjaman_ruangan' => PeminjamanTempat::whereMonth('created_at', date('m'))->count(),
                'peminjaman_barang' => PeminjamanBarang::whereMonth('created_at', date('m'))->count(),
            ];
            return view('dashboard.sarpras', compact('stats'));
        }
        
        elseif ($role === 'admin') {
            $stats = [
                'total_users' => User::count(),
            ];
            return view('dashboard.admin', compact('stats'));
        }

        // Fallback default
        return view('dashboard');
    }
}
