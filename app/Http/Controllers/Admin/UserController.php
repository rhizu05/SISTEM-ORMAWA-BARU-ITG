<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChangedMail;
use App\Models\Letter;
use App\Models\PeriodeAnggaran;
use App\Models\PasswordResetLog;
use App\Models\SaldoHistori;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Mendapatkan daftar role yang boleh dikelola oleh pengguna yang sedang login.
     */
    protected function getAllowedRoleNames(): array
    {
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->hasRole('admin')) {
            return ['admin', 'bkhm', 'wr3', 'bendahara', 'sarpras', 'ormawa', 'bem', 'bpm'];
        }

        // BKHM hanya mengelola role ormawa dan pejabat kemahasiswaan (tanpa admin, bkhm, dan mahasiswa)
        return ['ormawa', 'bem', 'bpm', 'wr3', 'bendahara', 'sarpras'];
    }

    public function index()
    {
        $users = User::with('roles')->where('id', '!=', auth()->id())->latest()->paginate(10);
        $allowedRoleNames = $this->getAllowedRoleNames();
        $roles = Role::whereIn('name', $allowedRoleNames)
            ->get()
            ->sortBy(function ($role) use ($allowedRoleNames) {
                return array_search($role->name, $allowedRoleNames);
            })
            ->values();
        $saldoHistori = SaldoHistori::with(['user', 'actor'])->latest()->take(20)->get();

        return view('admin.users.index', compact('users', 'roles', 'saldoHistori'));
    }

    public function store(Request $request)
    {
        $allowedRoles = $this->getAllowedRoleNames();
        $isOrmawaRole = in_array($request->role, ['ormawa', 'bem', 'bpm']);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'username' => ['required', 'string', 'max:50', 'unique:'.User::class],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'password' => ['required', Rules\Password::defaults()],
            'saldo' => ['nullable', 'numeric', 'min:0'],
            'file_sk' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'nomor_sk' => ['nullable', 'string', 'max:255'],
            'tanggal_sk' => ['nullable', 'date'],
        ];

        $request->validate($rules);

        $skPath = null;
        if ($request->hasFile('file_sk')) {
            $file = $request->file('file_sk');
            $filename = time() . '_' . Str::random(6) . '_SK_' . Str::slug($request->username) . '.pdf';
            $skPath = $file->storeAs('sk_ormawa', $filename, 'local');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'saldo' => $request->saldo ?? 0,
            'saldo_awal' => $request->saldo ?? 0,
            'file_sk' => $skPath,
            'nomor_sk' => $request->nomor_sk,
            'tanggal_sk' => $request->tanggal_sk ?? now()->toDateString(),
        ]);

        $user->assignRole($request->role);

        // Otomatis arsipkan Surat Keputusan (SK) ke tabel letters
        if ($skPath) {
            Letter::create([
                'user_id' => $user->id,
                'type' => 'sk_kepengurusan',
                'nomor_surat' => $request->nomor_sk ?: ('SK/' . date('Y') . '/' . strtoupper($user->username)),
                'perihal' => 'Surat Keputusan (SK) Pengesahan Kepengurusan ' . $user->name,
                'content' => 'Dokumen resmi Surat Keputusan (SK) Pengesahan Kepengurusan ' . $user->name . ' yang diverifikasi dan diarsipkan oleh BKHM Institut Teknologi Garut.',
                'metadata' => [
                    'file_path' => $skPath,
                    'original_name' => $request->file('file_sk')->getClientOriginalName(),
                    'file_size' => $request->file('file_sk')->getSize(),
                    'tanggal_sk' => $request->tanggal_sk ?? now()->toDateString(),
                    'uploaded_by' => auth()->id(),
                    'uploaded_by_name' => auth()->user()?->name ?? 'BKHM ITG',
                    'is_sk' => true,
                ],
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()?->hasRole('admin') && $user->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah akun administrator.');
        }

        $allowedRoles = $this->getAllowedRoleNames();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'username' => ['required', 'string', 'max:50', 'unique:'.User::class.',username,'.$user->id],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'status_akun' => ['required', 'in:aktif,nonaktif'],
            'password' => ['nullable', Rules\Password::defaults()],
            'file_sk' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'nomor_sk' => ['nullable', 'string', 'max:255'],
            'tanggal_sk' => ['nullable', 'date'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->status_akun = $request->status_akun;

        if ($request->filled('nomor_sk')) {
            $user->nomor_sk = $request->nomor_sk;
        }
        if ($request->filled('tanggal_sk')) {
            $user->tanggal_sk = $request->tanggal_sk;
        }

        if ($request->hasFile('file_sk')) {
            if ($user->file_sk && Storage::disk('local')->exists($user->file_sk)) {
                Storage::disk('local')->delete($user->file_sk);
            }
            $file = $request->file('file_sk');
            $filename = time() . '_' . Str::random(6) . '_SK_' . Str::slug($request->username) . '.pdf';
            $user->file_sk = $file->storeAs('sk_ormawa', $filename, 'local');

            // Update atau buat Letter record
            Letter::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => 'sk_kepengurusan',
                ],
                [
                    'nomor_surat' => $user->nomor_sk ?: ('SK/' . date('Y') . '/' . strtoupper($user->username)),
                    'perihal' => 'Surat Keputusan (SK) Pengesahan Kepengurusan ' . $user->name,
                    'content' => 'Dokumen resmi Surat Keputusan (SK) Pengesahan Kepengurusan ' . $user->name . ' yang diverifikasi dan diarsipkan oleh BKHM Institut Teknologi Garut.',
                    'metadata' => [
                        'file_path' => $user->file_sk,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'tanggal_sk' => $user->tanggal_sk ?? now()->toDateString(),
                        'uploaded_by' => auth()->id(),
                        'uploaded_by_name' => auth()->user()?->name ?? 'BKHM ITG',
                        'is_sk' => true,
                    ],
                ]
            );
        }
        
        $passwordChanged = (bool) $request->password;

        if ($passwordChanged) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        $user->syncRoles([$request->role]);

        if ($passwordChanged) {
            PasswordResetLog::create([
                'user_id' => $user->id,
                'actor_id' => auth()->id(),
                'actor_role' => auth()->user()?->getRoleNames()->first() ?? 'admin',
                'ip_address' => $request->ip(),
            ]);

            try {
                Mail::to($user->email)->send(new PasswordChangedMail($user, 'panel administrasi BKHM/Admin'));
            } catch (\Throwable $e) {
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    public function updateSaldo(Request $request, User $user)
    {
        $request->validate([
            'saldo' => ['required', 'numeric', 'min:0'],
            'catatan' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $user) {
            $before = (float) $user->saldo;
            $after = (float) $request->saldo;

            $user->update(['saldo' => $after]);

            SaldoHistori::create([
                'user_id' => $user->id,
                'actor_id' => auth()->id(),
                'periode_anggaran_id' => PeriodeAnggaran::aktif()?->id,
                'tipe' => 'koreksi',
                'nominal_sebelum' => $before,
                'nominal_sesudah' => $after,
                'selisih' => $after - $before,
                'catatan' => $request->catatan,
            ]);
        });

        return redirect()->route('admin.users.index')->with('success', 'Saldo berhasil diperbarui dan dicatat.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus diri sendiri.');
        }

        if (!auth()->user()?->hasRole('admin') && $user->hasRole('admin')) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus akun administrator.');
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
