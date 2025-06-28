<?php

namespace App\Http\Controllers;

use App\Models\RapotHimpunan;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RapotHimpunanController extends Controller
{
    private function canManageAll($user)
    {
        return in_array($user->role, ['BPH', 'SEKERTARIS_KABINET']);
    }

    public function rapotHmjtIndex(Request $request)
    {
        $query = RapotHimpunan::with('user')->select('rapot_himpunans.*');

        // Filter berdasarkan role
        $user = Auth::user();
        if (!$this->canManageAll($user)) {
            // Jika bukan BPH atau Sekretaris Kabinet, hanya tampilkan data milik sendiri
            $query->where('user_id', $user->id);
        }

        // Pencarian
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', "%{$searchTerm}%");
                })
                    ->orWhere('jabatan', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Paginasi
        $perPage = $request->input('per_page', 10);
        $rapotHmjt = $query->latest()->paginate($perPage);

        // Get users for dropdown
        // Jika BPH atau Sekretaris Kabinet, tampilkan semua user
        // Jika tidak, hanya tampilkan user yang sedang login
        $usersQuery = User::select('id', 'name');
        if (!$this->canManageAll($user)) {
            $usersQuery->where('id', $user->id);
        }
        $users = $usersQuery->get();

        return Inertia::render('Rapot/Index', [
            'rapotHmjt' => $rapotHmjt,
            'users' => $users,
            'canManageAll' => $this->canManageAll($user),
        ]);
    }

    public function rapotHmjtStore(Request $request)
    {
        $user = Auth::user();

        // Validasi akses
        if (!$this->canManageAll($user)) {
            // Jika bukan BPH atau Sekretaris Kabinet, hanya bisa input data sendiri
            if ($request->user_id != $user->id) {
                abort(403, 'Anda hanya dapat menambahkan data untuk diri sendiri.');
            }
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'jabatan' => 'required|string|max:255',
            'periode_awal' => ['required', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'periode_akhir' => [
                'required',
                'string',
                'regex:/^\d{4}-(0[1-9]|1[0-2])$/',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value < $request->periode_awal) {
                        $fail('Periode akhir harus setelah periode awal.');
                    }
                }
            ],
            'file' => 'required|file|max:10240', // maksimal 10MB
            'description' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $path = $file->store('rapot-hmjt', 'public');

        RapotHimpunan::create([
            'user_id' => $request->user_id,
            'jabatan' => $request->jabatan,
            'periode_awal' => $request->periode_awal,
            'periode_akhir' => $request->periode_akhir,
            'file_path' => $path,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Rapot HMJTK berhasil ditambahkan');
    }

    public function rapotHmjtUpdate(Request $request, RapotHimpunan $rapotHmjt)
    {
        $user = Auth::user();

        // Validasi akses
        if (!$this->canManageAll($user)) {
            // Jika bukan BPH atau Sekretaris Kabinet, hanya bisa edit data sendiri
            if ($rapotHmjt->user_id != $user->id || $request->user_id != $user->id) {
                abort(403, 'Anda hanya dapat mengubah data milik sendiri.');
            }
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'jabatan' => 'required|string|max:255',
            'periode_awal' => ['required', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'periode_akhir' => [
                'required',
                'string',
                'regex:/^\d{4}-(0[1-9]|1[0-2])$/',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value < $request->periode_awal) {
                        $fail('Periode akhir harus setelah periode awal.');
                    }
                }
            ],
            'file' => 'nullable|file|max:10240', // maksimal 10MB
            'description' => 'nullable|string'
        ]);

        $data = [
            'user_id' => $request->user_id,
            'jabatan' => $request->jabatan,
            'periode_awal' => $request->periode_awal,
            'periode_akhir' => $request->periode_akhir,
            'description' => $request->description
        ];

        if ($request->hasFile('file')) {
            // Hapus file lama
            Storage::disk('public')->delete($rapotHmjt->file_path);

            // Upload file baru
            $file = $request->file('file');
            $path = $file->store('rapot-hmjt', 'public');
            $data['file_path'] = $path;
        }

        $rapotHmjt->update($data);

        return redirect()->back()->with('success', 'Rapot HMJTK berhasil diperbarui');
    }

    public function rapotHmjtDestroy(RapotHimpunan $rapotHmjt)
    {
        $user = Auth::user();

        // Validasi akses
        if (!$this->canManageAll($user)) {
            // Jika bukan BPH atau Sekretaris Kabinet, hanya bisa hapus data sendiri
            if ($rapotHmjt->user_id != $user->id) {
                abort(403, 'Anda hanya dapat menghapus data milik sendiri.');
            }
        }

        // Hapus file
        Storage::disk('public')->delete($rapotHmjt->file_path);

        // Hapus record
        $rapotHmjt->delete();

        return redirect()->back()->with('success', 'Rapot HMJTK berhasil dihapus');
    }
}
