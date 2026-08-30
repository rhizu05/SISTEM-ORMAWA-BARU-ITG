<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\MasterBarang;
use Illuminate\Http\Request;

class MasterBarangController extends Controller
{
    public function index()
    {
        $barangs = MasterBarang::orderBy('nama_barang', 'asc')->paginate(10);
        return view('sarpras.barang.index', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'stok_tersedia' => 'required|integer|min:0',
            'status_aktif' => 'boolean'
        ]);

        MasterBarang::create([
            'nama_barang' => $request->nama_barang,
            'stok_tersedia' => $request->stok_tersedia,
            'status_aktif' => $request->has('status_aktif') ? 1 : 0
        ]);

        return redirect()->back()->with('success', 'Barang inventaris berhasil ditambahkan.');
    }

    public function update(Request $request, MasterBarang $barang)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'stok_tersedia' => 'required|integer|min:0',
            'status_aktif' => 'boolean'
        ]);

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'stok_tersedia' => $request->stok_tersedia,
            'status_aktif' => $request->has('status_aktif') ? 1 : 0
        ]);

        return redirect()->back()->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(MasterBarang $barang)
    {
        $barang->delete();
        return redirect()->back()->with('success', 'Barang inventaris berhasil dihapus.');
    }
}
