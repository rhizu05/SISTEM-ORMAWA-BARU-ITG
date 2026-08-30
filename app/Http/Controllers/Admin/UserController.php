<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->where('id', '!=', auth()->id())->latest()->paginate(10);
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'username' => ['required', 'string', 'max:50', 'unique:'.User::class],
            'role' => ['required', 'exists:roles,name'],
            'password' => ['required', Rules\Password::defaults()],
            'saldo' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'saldo' => $request->saldo ?? 0,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'username' => ['required', 'string', 'max:50', 'unique:'.User::class.',username,'.$user->id],
            'role' => ['required', 'exists:roles,name'],
            'status_akun' => ['required', 'in:aktif,nonaktif'],
            'password' => ['nullable', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->status_akun = $request->status_akun;
        
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    public function updateSaldo(Request $request, User $user)
    {
        $request->validate([
            'saldo' => ['required', 'numeric', 'min:0'],
        ]);

        $user->update([
            'saldo' => $request->saldo
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Saldo berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus diri sendiri.');
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
