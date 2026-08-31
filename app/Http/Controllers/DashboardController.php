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
            // Calculate Total Dana Diberikan (Sum of all approved funding for this user via Pengajuan)
            $totalDanaDiberikan = \App\Models\Dana::whereHas('pengajuan', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->sum('nominal_cair');
            
            // Calculate Dana Diproses (Sum of approved funding not yet fully disbursed or in process)
            $danaDiproses = Pengajuan::where('user_id', $user->id)
                ->whereHas('state', function($q) {
                    $q->where('name', 'approved'); // Assuming 'approved' is the state before disbursement
                })->sum('dana_diajukan');

            $stats = [
                'total_pengajuan' => Pengajuan::where('user_id', $user->id)->count(),
                'saldo' => $user->saldo,
                'total_dana' => $totalDanaDiberikan,
                'dana_diproses' => $danaDiproses,
                'sedang_proses' => Pengajuan::where('user_id', $user->id)->whereHas('state', function($q) {
                    $q->whereNotIn('name', ['draft', 'completed', 'rejected']);
                })->count(),
            ];

            // Fetch data for widgets - fix column names sesuai schema
            $meetings = \App\Models\JadwalRapat::where('tanggal_rapat', '>=', now()->toDateString())
                ->orderBy('tanggal_rapat', 'asc')
                ->take(5)
                ->get();

            $facilities = \App\Models\PeminjamanTempat::with('ruangan')->where('user_id', $user->id)
                ->where('tgl_mulai', '>=', now()->toDateString())
                ->orderBy('tgl_mulai', 'asc')
                ->take(10)
                ->get();

            return view('dashboard.ormawa', compact('stats', 'meetings', 'facilities'));
        }
        
        elseif ($role === 'bkh') {
            // BKKH Dashboard khusus
            $counts = [
                'verifikasi_proposal' => Pengajuan::whereHas('state', fn($q)=>$q->where('name','bkh_review'))->count(),
                'verifikasi_lpj' => \App\Models\Letter::where('type','lpj')->where('created_at','>=', now()->subMonths(3))->count(),
                'siap_bendahara' => Pengajuan::whereHas('state', fn($q)=>$q->where('name','to_treasurer'))->count(),
                'verifikasi_tempat' => PeminjamanTempat::where('status_bkkh','pending')->count(),
                'verifikasi_barang' => PeminjamanBarang::where('status_bkkh','pending')->count(),
            ];
            $rapats = \App\Models\JadwalRapat::with('penyelenggara')->latest()->take(10)->get();
            $proposalQueue = Pengajuan::with(['user','state'])->whereHas('state', fn($q)=>$q->where('name','bkh_review'))->latest()->take(10)->get();
            $tempatQueue = PeminjamanTempat::with(['user','ruangan'])->where('status_bkkh','pending')->latest()->take(10)->get();
            $barangQueue = PeminjamanBarang::with('user')->where('status_bkkh','pending')->latest()->take(10)->get();
            // kalender terpadu
            $calendarTempat = PeminjamanTempat::with('ruangan')->whereIn('status_akhir',['Selesai / Disetujui','Proses Sarpras'])->get();
            $calendarBarang = PeminjamanBarang::whereIn('status_akhir',['Selesai / Disetujui','Proses Sarpras'])->get();
            return view('dashboard.bkkh', compact('counts','rapats','proposalQueue','tempatQueue','barangQueue','calendarTempat','calendarBarang'));
        }
        elseif ($role === 'bem') {
            $saldoAwal = $user->saldo_awal ?? $user->saldo;
            $terpakai = max(0, $saldoAwal - $user->saldo);
            $counts = [
                'verifikasi_proposal' => Pengajuan::whereHas('state', fn($q)=>$q->where('name','bem_review'))->count(),
            ];
            $rapats = \App\Models\JadwalRapat::with('penyelenggara')->latest()->take(10)->get();
            $proposalQueue = Pengajuan::with(['user','state'])->whereHas('state', fn($q)=>$q->where('name','bem_review'))->latest()->take(10)->get();
            $calendarTempat = PeminjamanTempat::with('ruangan')->whereIn('status_akhir',['Selesai / Disetujui','Proses Sarpras'])->get();
            $calendarBarang = PeminjamanBarang::whereIn('status_akhir',['Selesai / Disetujui','Proses Sarpras'])->get();
            return view('dashboard.bem', compact('saldoAwal','terpakai','rapats','counts','proposalQueue','calendarTempat','calendarBarang'));
        }
        elseif ($role === 'bpm') {
            $saldoAwal = $user->saldo_awal ?? $user->saldo;
            $terpakai = max(0, $saldoAwal - $user->saldo);
            $counts = [
                'verifikasi_proposal' => Pengajuan::whereHas('state', fn($q)=>$q->where('name','bpm_review'))->count(),
            ];
            $rapats = \App\Models\JadwalRapat::with('penyelenggara')->latest()->take(10)->get();
            $proposalQueue = Pengajuan::with(['user','state'])->whereHas('state', fn($q)=>$q->where('name','bpm_review'))->latest()->take(10)->get();
            $calendarTempat = PeminjamanTempat::with('ruangan')->whereIn('status_akhir',['Selesai / Disetujui','Proses Sarpras'])->get();
            $calendarBarang = PeminjamanBarang::whereIn('status_akhir',['Selesai / Disetujui','Proses Sarpras'])->get();
            return view('dashboard.bpm', compact('saldoAwal','terpakai','rapats','counts','proposalQueue','calendarTempat','calendarBarang'));
        }
        elseif ($role === 'wr3') {
            $usersWithSaldo = User::whereNotNull('saldo_awal')
                ->get()
                ->map(function($u) {
                    $terpakai = max(0, $u->saldo_awal - $u->saldo);
                    return [
                        'name' => $u->name,
                        'role' => $u->roles->first()?->name ?? 'none',
                        'saldo_awal' => $u->saldo_awal,
                        'saldo' => $u->saldo,
                        'terpakai' => $terpakai,
                    ];
                });
            
            // Group by role
            $saldoByRole = [];
            foreach ($usersWithSaldo as $u) {
                $role = $u['role'];
                if (!isset($saldoByRole[$role])) {
                    $saldoByRole[$role] = [];
                }
                $saldoByRole[$role][] = $u;
            }
            
            $rapats = \App\Models\JadwalRapat::latest()->take(10)->get();
            $proposalQueue = Pengajuan::with(['user','state'])->whereHas('state', fn($q)=>$q->where('name','bem_review'))->latest()->take(10)->get();
            $calendarTempat = PeminjamanTempat::with('ruangan')->whereIn('status_akhir',['Selesai / Disetujui','Proses Sarpras'])->get();
            $calendarBarang = PeminjamanBarang::whereIn('status_akhir',['Selesai / Disetujui','Proses Sarpras'])->get();
            
            return view('dashboard.wr3', compact('usersWithSaldo', 'saldoByRole', 'rapats', 'proposalQueue', 'calendarTempat', 'calendarBarang'));
        }
        
        elseif ($role === 'bendahara') {
            $siapCairQueue = Pengajuan::with('user')
                ->whereHas('state', fn($q) => $q->where('name', 'to_treasurer'))
                ->latest()
                ->get();

            $stats = [
                'siap_cair' => $siapCairQueue->count(),
                'total_dicairkan' => \App\Models\Dana::sum('nominal_cair'),
            ];
            return view('dashboard.bendahara', compact('stats', 'siapCairQueue'));
        }
        
        elseif (in_array($role, ['sarpras_ruangan', 'sarpras_barang'])) {
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
