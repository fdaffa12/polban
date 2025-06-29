<?php

namespace App\Http\Controllers;

use App\Models\Notulensi;
use App\Models\FormatAdministrasi;
use App\Models\BukuPanduan;
use App\Models\KontentEkslusif;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;

class AdministrationController extends Controller
{
    private function hasAdministrationAccess($user)
    {
        return in_array($user->role, [
            'BPH',
            'SEKERTARIS_BENDAHARA',
            'SEKERTARIS_KABINET',
            'SEKERTARIS_UMUM_MPH'
        ]);
    }
    public function notulensiIndex(Request $request)
    {
        $user = Auth::user();

        // Check if user has access to notulensi
        if (!$this->hasAdministrationAccess($user)) {
            abort(403, 'Unauthorized action.');
        }

        $query = Notulensi::with('department');

        // If user is not BPH, only show notulensi from their department
        if ($user->role !== 'BPH' && $user->role !== 'SEKERTARIS_KABINET') {
            $query->where('department_id', $user->department_id);
        }

        // Filter by department if selected (only for BPH)
        if (($user->role === 'BPH' || $user->role === 'SEKERTARIS_KABINET') && $request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

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

        // Get departments for filter based on user role
        if ($user->role === 'BPH' || $user->role === 'SEKERTARIS_KABINET') {
            // For BPH, get only departments that have notulensi records for filter
            $departmentIds = Notulensi::distinct('department_id')
                ->whereNotNull('department_id')
                ->pluck('department_id');

            $filterDepartments = Department::whereIn('id', $departmentIds)
                ->orderByRaw("
                    CASE 
                        WHEN dept_name = 'BPH (BEH)' THEN 1
                        WHEN dept_name = 'Biro Medkominfo' THEN 2
                        WHEN dept_name = 'Biro Bisnis' THEN 3
                        ELSE 4 
                    END
                ")
                ->get();
        } else {
            // For non-BPH, only get their department
            $filterDepartments = Department::where('id', $user->department_id)->get();
        }

        // Get all departments for input modal
        $allDepartments = Department::orderByRaw("
            CASE 
                WHEN dept_name = 'BPH (BEH)' THEN 1
                WHEN dept_name = 'Biro Medkominfo' THEN 2
                WHEN dept_name = 'Biro Bisnis' THEN 3
                ELSE 4 
            END
        ")->get();

        return Inertia::render('Administration/Notulensi', [
            'notulensi' => $notulensi,
            'departments' => $filterDepartments,
            'allDepartments' => $allDepartments,
            'filters' => [
                'department_id' => $request->department_id,
            ],
            'userRole' => $user->role,
        ]);
    }

    public function notulensiStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // maksimal 10MB
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id'
        ]);

        $file = $request->file('file');
        $path = $file->store('notulensi', 'public');

        Notulensi::create([
            'title' => $request->title,
            'file_path' => $path,
            'description' => $request->description,
            'department_id' => $request->department_id
        ]);

        return redirect()->back()->with('success', 'Notulensi berhasil ditambahkan');
    }

    public function notulensiUpdate(Request $request, Notulensi $notulensi)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|max:10240', // maksimal 10MB
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id'
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'department_id' => $request->department_id
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
        $user = Auth::user();

        // Check if user has access to format administrasi
        if (!$this->hasAdministrationAccess($user)) {
            abort(403, 'Unauthorized action.');
        }

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

    public function bukuPanduanIndex(Request $request)
    {
        $user = Auth::user();

        // Check if user has access to buku panduan
        if (!$this->hasAdministrationAccess($user)) {
            abort(403, 'Unauthorized action.');
        }

        $query = BukuPanduan::query();

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
        $bukuPanduan = $query->latest()->paginate($perPage);

        return Inertia::render('Administration/BukuPanduan', [
            'bukuPanduan' => $bukuPanduan
        ]);
    }

    public function bukuPanduanStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // maksimal 10MB
            'description' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $path = $file->store('buku-panduan', 'public');

        BukuPanduan::create([
            'title' => $request->title,
            'file_path' => $path,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Buku panduan berhasil ditambahkan');
    }

    public function bukuPanduanUpdate(Request $request, BukuPanduan $bukuPanduan)
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
            Storage::disk('public')->delete($bukuPanduan->file_path);

            // Upload file baru
            $file = $request->file('file');
            $path = $file->store('buku-panduan', 'public');
            $data['file_path'] = $path;
        }

        $bukuPanduan->update($data);

        return redirect()->back()->with('success', 'Buku panduan berhasil diperbarui');
    }

    public function bukuPanduanDestroy(BukuPanduan $bukuPanduan)
    {
        // Hapus file
        Storage::disk('public')->delete($bukuPanduan->file_path);

        // Hapus record
        $bukuPanduan->delete();

        return redirect()->back()->with('success', 'Buku panduan berhasil dihapus');
    }

    public function kontenEkslusifIndex(Request $request)
    {
        $query = KontentEkslusif::query();

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
        $kontenEkslusif = $query->latest()->paginate($perPage);

        return Inertia::render('Administration/KontenEkslusif', [
            'kontenEkslusif' => $kontenEkslusif
        ]);
    }

    public function kontenEkslusifStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // maksimal 10MB
            'description' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $path = $file->store('konten-ekslusif', 'public');

        KontentEkslusif::create([
            'title' => $request->title,
            'file_path' => $path,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Konten ekslusif berhasil ditambahkan');
    }

    public function kontenEkslusifUpdate(Request $request, KontentEkslusif $kontenEkslusif)
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
            Storage::disk('public')->delete($kontenEkslusif->file_path);

            // Upload file baru
            $file = $request->file('file');
            $path = $file->store('konten-ekslusif', 'public');
            $data['file_path'] = $path;
        }

        $kontenEkslusif->update($data);

        return redirect()->back()->with('success', 'Konten ekslusif berhasil diperbarui');
    }

    public function kontenEkslusifDestroy(KontentEkslusif $kontenEkslusif)
    {
        // Hapus file
        Storage::disk('public')->delete($kontenEkslusif->file_path);

        // Hapus record
        $kontenEkslusif->delete();

        return redirect()->back()->with('success', 'Konten ekslusif berhasil dihapus');
    }
}