<?php

namespace App\Http\Controllers;

use App\Models\Notulensi;
use App\Models\FormatAdministrasi;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class AdministrationController extends Controller
{
    public function notulensiIndex(Request $request)
    {
        $query = Notulensi::query();

        // Pencarian
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Paginasi
        $perPage = $request->input('per_page', 10);
        $notulensi = $query->latest()->paginate($perPage);

        return Inertia::render('Administration/Notulensi', [
            'notulensi' => $notulensi
        ]);
    }

    public function notulensiStore(Request $request)
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

    public function notulensiUpdate(Request $request, Notulensi $notulensi)
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

    public function notulensiDestroy(Notulensi $notulensi)
    {
        // Hapus file
        Storage::disk('public')->delete($notulensi->file_path);

        // Hapus record
        $notulensi->delete();

        return redirect()->back()->with('success', 'Notulensi berhasil dihapus');
    }

    public function formatAdministrasiIndex(Request $request)
    {
        $query = FormatAdministrasi::query();

        // Pencarian
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Paginasi
        $perPage = $request->input('per_page', 10);
        $formatAdministrasi = $query->latest()->paginate($perPage);

        return Inertia::render('Administration/FormatAdministrasi', [
            'formatAdministrasi' => $formatAdministrasi
        ]);
    }

    public function formatAdministrasiStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // maksimal 10MB
            'description' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $path = $file->store('format-administrasi', 'public');

        FormatAdministrasi::create([
            'title' => $request->title,
            'file_path' => $path,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Format administrasi berhasil ditambahkan');
    }

    public function formatAdministrasiUpdate(Request $request, FormatAdministrasi $formatAdministrasi)
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
            Storage::disk('public')->delete($formatAdministrasi->file_path);

            // Upload file baru
            $file = $request->file('file');
            $path = $file->store('format-administrasi', 'public');
            $data['file_path'] = $path;
        }

        $formatAdministrasi->update($data);

        return redirect()->back()->with('success', 'Format administrasi berhasil diperbarui');
    }

    public function formatAdministrasiDestroy(FormatAdministrasi $formatAdministrasi)
    {
        // Hapus file
        Storage::disk('public')->delete($formatAdministrasi->file_path);

        // Hapus record
        $formatAdministrasi->delete();

        return redirect()->back()->with('success', 'Format administrasi berhasil dihapus');
    }
}