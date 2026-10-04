<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\PeminjamanTempat;
use App\Models\PeminjamanBarang;
use App\Models\User;
use App\Models\SaldoHistori;
use App\Models\WorkflowState;
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
                    $q->where('name', WorkflowState::TO_TREASURER); // disetujui WR3, menunggu pencairan
                })->sum('dana_diajukan');

            $stats = [
                'total_pengajuan' => Pengajuan::where('user_id', $user->id)->count(),
                'saldo' => $user->saldo,
                'total_dana' => $totalDanaDiberikan,
                'dana_diproses' => $danaDiproses,
                'sedang_proses' => Pengajuan::where('user_id', $user->id)->whereHas('state', function($q) {
                    $q->whereNotIn('name', [WorkflowState::DRAFT, WorkflowState::COMPLETED, WorkflowState::REJECTED, WorkflowState::CANCELLED]);
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

            $menungguLpj = Pengajuan::where('user_id', $user->id)
                ->whereHas('state', fn($q)=>$q->where('name', WorkflowState::FUNDS_DISBURSED))
                ->latest()
                ->get();

            // Surat Peringatan aktif yang ditujukan untuk ormawa ini
            $spAktif = \App\Models\SuratPeringatan::with('creator')
                ->where('target_user_id', $user->id)
                ->latest()
                ->get();

            return view('dashboard.ormawa', compact('stats', 'meetings', 'facilities', 'menungguLpj', 'spAktif'));
        }
        
        elseif ($role === 'bkhm') {
            // BKHM Dashboard khusus
            $counts = [
                'verifikasi_proposal' => Pengajuan::whereHas('state', fn($q)=>$q->where('name',WorkflowState::BPM_APPROVED))->count(),
                'verifikasi_lpj' => Pengajuan::whereHas('state', fn($q)=>$q->where('name', WorkflowState::LPJ_SUBMITTED))->count(),
                'siap_bendahara' => Pengajuan::whereHas('state', fn($q)=>$q->where('name',WorkflowState::WR3_APPROVED))->count(),
                'verifikasi_tempat' => PeminjamanTempat::where('status_bkhm','pending')->count(),
                'verifikasi_barang' => PeminjamanBarang::where('status_bkhm','pending')->count(),
                'kurasi_berita' => \App\Models\Pengumuman::pendingKurasi()->count(),
            ];
            $rapats = \App\Models\JadwalRapat::with('penyelenggara')->latest()->take(10)->get();
            $proposalQueue = Pengajuan::with(['user','state'])->whereHas('state', fn($q)=>$q->where('name',WorkflowState::BPM_APPROVED))->latest()->take(10)->get();
            $lpjQueue = Pengajuan::with(['user','state','dana'])->whereHas('state', fn($q)=>$q->where('name', WorkflowState::LPJ_SUBMITTED))->latest()->take(10)->get();
            $tempatQueue = PeminjamanTempat::with(['user','ruangan'])->where('status_bkhm','pending')->latest()->take(10)->get();
            $barangQueue = PeminjamanBarang::with('user')->where('status_bkhm','pending')->latest()->take(10)->get();
            // kalender terpadu
            $calendarTempat = PeminjamanTempat::with('ruangan')->whereIn('status_akhir',['Selesai / Disetujui','Proses Sarpras'])->get();
            $calendarBarang = PeminjamanBarang::whereIn('status_akhir',['Sedang Digunakan','Proses Sarpras'])->get();
            $saldoHistori = SaldoHistori::with(['user', 'actor'])->latest()->take(20)->get();
            return view('dashboard.bkhm', compact('counts','rapats','proposalQueue','lpjQueue','tempatQueue','barangQueue','calendarTempat','calendarBarang','saldoHistori'));
        }
        elseif ($role === 'bem') {
            $saldoAwal = $user->saldo_awal ?? $user->saldo;
            $terpakai = max(0, $saldoAwal - $user->saldo);
            $counts = [
                'verifikasi_proposal' => Pengajuan::whereHas('state', fn($q)=>$q->where('name',WorkflowState::SUBMITTED))->count(),
            ];
            $rapats = \App\Models\JadwalRapat::with('penyelenggara')->latest()->take(10)->get();
            $proposalQueue = Pengajuan::with(['user','state'])->whereHas('state', fn($q)=>$q->where('name',WorkflowState::SUBMITTED))->latest()->take(10)->get();
            $calendarTempat = PeminjamanTempat::with('ruangan')->whereIn('status_akhir',['Selesai / Disetujui','Proses Sarpras'])->get();
            $calendarBarang = PeminjamanBarang::whereIn('status_akhir',['Sedang Digunakan','Proses Sarpras'])->get();
            return view('dashboard.bem', compact('saldoAwal','terpakai','rapats','counts','proposalQueue','calendarTempat','calendarBarang'));
        }
        elseif ($role === 'bpm') {
            $saldoAwal = $user->saldo_awal ?? $user->saldo;
            $terpakai = max(0, $saldoAwal - $user->saldo);
            $counts = [
                'verifikasi_proposal' => Pengajuan::whereHas('state', fn($q)=>$q->where('name',WorkflowState::BEM_APPROVED))->count(),
                'aspirasi_masuk' => \App\Models\TiketLayanan::where('kategori', 'aspirasi')->where('status', 'pending')->count(),
            ];
            $rapats = \App\Models\JadwalRapat::with('penyelenggara')->latest()->take(10)->get();
            $proposalQueue = Pengajuan::with(['user','state'])->whereHas('state', fn($q)=>$q->where('name',WorkflowState::BEM_APPROVED))->latest()->take(10)->get();
            $calendarTempat = PeminjamanTempat::with('ruangan')->whereIn('status_akhir',['Selesai / Disetujui','Proses Sarpras'])->get();
            $calendarBarang = PeminjamanBarang::whereIn('status_akhir',['Sedang Digunakan','Proses Sarpras'])->get();
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
            $proposalQueue = Pengajuan::with(['user','state'])->whereHas('state', fn($q)=>$q->where('name',WorkflowState::BKHM_APPROVED))->latest()->take(10)->get();
            $calendarTempat = PeminjamanTempat::with('ruangan')->whereIn('status_akhir',['Selesai / Disetujui','Proses Sarpras'])->get();
            $calendarBarang = PeminjamanBarang::whereIn('status_akhir',['Sedang Digunakan','Proses Sarpras'])->get();
            
            $saldoHistori = SaldoHistori::with(['user', 'actor'])->latest()->take(20)->get();
            $pendingSpCount = \App\Models\SuratPeringatan::menungguValidasi()->count();
            $pendingSpQueue = \App\Models\SuratPeringatan::with(['target', 'creator'])->menungguValidasi()->latest()->take(5)->get();

            return view('dashboard.wr3', compact('usersWithSaldo', 'saldoByRole', 'rapats', 'proposalQueue', 'calendarTempat', 'calendarBarang', 'saldoHistori', 'pendingSpCount', 'pendingSpQueue'));
        }
        
        elseif ($role === 'bendahara') {
            $siapCairQueue = Pengajuan::with('user')
                ->whereHas('state', fn($q) => $q->where('name', WorkflowState::TO_TREASURER))
                ->latest()
                ->get();

            $riwayatPencairan = \App\Models\Dana::with(['pengajuan.user'])
                ->latest('tanggal_cair')
                ->take(15)
                ->get();

            $stats = [
                'siap_cair' => $siapCairQueue->count(),
                'total_dicairkan' => \App\Models\Dana::sum('nominal_cair'),
            ];
            return view('dashboard.bendahara', compact('stats', 'siapCairQueue', 'riwayatPencairan'));
        }
        
        elseif ($role === 'sarpras') {
            $stats = [
                'peminjaman_ruangan' => PeminjamanTempat::whereMonth('created_at', date('m'))->count(),
                'peminjaman_barang' => PeminjamanBarang::whereMonth('created_at', date('m'))->count(),
            ];

            $antrianTempat = PeminjamanTempat::with(['user', 'ruangan'])
                ->where('status_sarpras', 'pending')
                ->latest()
                ->take(5)
                ->get();

            $antrianBarang = PeminjamanBarang::with('user')
                ->where('status_sarpras', 'pending')
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.sarpras', compact('stats', 'antrianTempat', 'antrianBarang'));
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
