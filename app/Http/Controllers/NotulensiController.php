<?php

namespace App\Http\Controllers;

use App\Models\Notulensi;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class NotulensiController extends Controller
{
    public function index()
    {
        return Inertia::render('Notulensi/Index', [
            'notulensi' => Notulensi::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // maksimal 10MB
            'description' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $path = $file->store('notulensi', 'public');

        Notulensi::create([
            'title' => $request->title,
            'file_path' => $path,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Notulensi berhasil ditambahkan');
    }

    public function update(Request $request, Notulensi $notulensi)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|max:10240', // maksimal 10MB
            'description' => 'nullable|string'
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description
        ];

        if ($request->hasFile('file')) {
            // Hapus file lama
            Storage::disk('public')->delete($notulensi->file_path);

            // Upload file baru
            $file = $request->file('file');
            $path = $file->store('notulensi', 'public');
            $data['file_path'] = $path;
        }

        $notulensi->update($data);

        return redirect()->back()->with('success', 'Notulensi berhasil diperbarui');
    }

    public function destroy(Notulensi $notulensi)
    {
        // Hapus file
        Storage::disk('public')->delete($notulensi->file_path);

        // Hapus record
        $notulensi->delete();

        return redirect()->back()->with('success', 'Notulensi berhasil dihapus');
    }
}