<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasis = Notifikasi::where('user_id', Auth::id())->latest()->paginate(15);
        return view('notifikasi.index', compact('notifikasis'));
    }

    public function markRead(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->user_id === Auth::id(), 403);

        $notifikasi->update(['status_baca' => 'sudah']);

        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllRead()
    {
        Notifikasi::where('user_id', Auth::id())->update(['status_baca' => 'sudah']);

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
