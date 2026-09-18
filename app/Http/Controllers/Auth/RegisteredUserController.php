<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * BR-01: Mahasiswa adalah Guest tanpa akun. Akun ormawa/internal dikelola terpusat oleh BKHM/Admin.
     * Pendaftaran akun mandiri dinonaktifkan.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('login')->with('status', 'Pendaftaran akun mandiri ditiadakan. Mahasiswa dapat langsung mengakses layanan (Aspirasi, Konseling, Prestasi) tanpa akun melalui Portal Layanan Mahasiswa. Akun pengurus dan ormawa diterbitkan terpusat oleh BKHM.');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('login')->with('status', 'Pendaftaran akun mandiri ditiadakan. Akun pengurus/ormawa diterbitkan secara resmi oleh BKHM.');
    }
}
