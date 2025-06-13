<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Services\GoogleDriveService;
use App\Mail\ProposalRevision;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProposalController extends Controller
{
    protected $googleDrive;

    public function __construct(GoogleDriveService $googleDrive)
    {
        $this->googleDrive = $googleDrive;
    }

    public function index()
    {
        return Inertia::render('Proposals/Index', [
            'proposals' => Proposal::with('department')->latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Proposals/Create', [
            'departments' => Department::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pic_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'nama_kegiatan' => 'required|string|max:255',
            'bidang_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|string|in:karakter,penalaran,peminatan,pengabdian',
            'department_id' => 'required|exists:departments,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'tempat_kegiatan' => 'required|string|max:255',
            'jumlah_peserta' => 'required|integer|min:1',
            'jumlah_panitia' => 'required|integer|min:1',
            'jumlah_spj' => 'required|integer|min:0',
            'dana_dipa_polban' => 'nullable|numeric|min:0',
            'dana_swadaya' => 'nullable|numeric|min:0',
            'dana_sponsor' => 'nullable|numeric|min:0',
            'pengisi_acara' => 'required|string|max:255',
            'sponsorship' => 'nullable|string|max:255',
            'media_partner' => 'nullable|string|max:255',
            'doc_proposal' => 'required|mimes:pdf|max:10240',
            'doc_berkegiatan_ketuplak' => 'required|mimes:pdf|max:10240',
            'doc_ormawa' => 'required|mimes:pdf|max:10240',
            'doc_sarana_prasarana' => 'required|mimes:pdf|max:10240',
            'link_surat_izin_ortu' => 'required|string|max:255',
            'poster' => 'required|image|max:2048',
            'caption_poster' => 'required|string',
        ]);

        try {
            // Upload files ke Google Drive
            $docProposal = $this->googleDrive->uploadFile($request->file('doc_proposal'), 'proposal');
            $docBerkegiatan = $this->googleDrive->uploadFile($request->file('doc_berkegiatan_ketuplak'), 'berkegiatan');
            $docOrmawa = $this->googleDrive->uploadFile($request->file('doc_ormawa'), 'ormawa');
            $docSaranaPrasarana = $this->googleDrive->uploadFile($request->file('doc_sarana_prasarana'), 'sarana');
            $poster = $this->googleDrive->uploadFile($request->file('poster'), 'poster');

            Proposal::create([
                'pic_name' => $request->pic_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'nama_kegiatan' => $request->nama_kegiatan,
                'bidang_kegiatan' => $request->bidang_kegiatan,
                'jenis_kegiatan' => $request->jenis_kegiatan,
                'department_id' => $request->department_id,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'tempat_kegiatan' => $request->tempat_kegiatan,
                'jumlah_peserta' => $request->jumlah_peserta,
                'jumlah_panitia' => $request->jumlah_panitia,
                'jumlah_spj' => $request->jumlah_spj,
                'dana_dipa_polban' => $request->dana_dipa_polban,
                'dana_swadaya' => $request->dana_swadaya,
                'dana_sponsor' => $request->dana_sponsor,
                'pengisi_acara' => $request->pengisi_acara,
                'sponsorship' => $request->sponsorship,
                'media_partner' => $request->media_partner,
                'doc_proposal' => $docProposal,
                'doc_berkegiatan_ketuplak' => $docBerkegiatan,
                'doc_ormawa' => $docOrmawa,
                'doc_sarana_prasarana' => $docSaranaPrasarana,
                'link_surat_izin_ortu' => $request->link_surat_izin_ortu,
                'poster' => $poster,
                'caption_poster' => $request->caption_poster,
                'status' => 'pending',
            ]);

            return redirect()->route('proposals.index')->with('success', 'Proposal berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat proposal: ' . $e->getMessage());
        }
    }

    public function show(Proposal $proposal)
    {
        $proposal->load(['department', 'reviewer', 'approver']);
        return Inertia::render('Proposals/Show', [
            'proposal' => $proposal
        ]);
    }

    public function edit(Proposal $proposal)
    {
        return Inertia::render('Proposals/Edit', [
            'proposal' => $proposal,
            'departments' => Department::all(),
        ]);
    }

    public function update(Request $request, Proposal $proposal)
    {
        try {
            // Validasi file yang diupload
            $request->validate([
                'doc_proposal' => 'nullable|mimes:pdf|max:10240',
                'doc_berkegiatan_ketuplak' => 'nullable|mimes:pdf|max:10240',
                'doc_ormawa' => 'nullable|mimes:pdf|max:10240',
                'doc_sarana_prasarana' => 'nullable|mimes:pdf|max:10240',
            ]);

            // Update file yang diupload
            if ($request->hasFile('doc_proposal')) {
                try {
                    if ($proposal->doc_proposal) {
                        $this->googleDrive->deleteFile($proposal->doc_proposal);
                    }
                    $proposal->doc_proposal = $this->googleDrive->uploadFile($request->file('doc_proposal'), 'proposal');
                    $proposal->save();
                } catch (\Exception $e) {
                    Log::error('Error updating doc_proposal: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Gagal memperbarui dokumen proposal');
                }
            }

            if ($request->hasFile('doc_berkegiatan_ketuplak')) {
                try {
                    if ($proposal->doc_berkegiatan_ketuplak) {
                        $this->googleDrive->deleteFile($proposal->doc_berkegiatan_ketuplak);
                    }
                    $proposal->doc_berkegiatan_ketuplak = $this->googleDrive->uploadFile($request->file('doc_berkegiatan_ketuplak'), 'berkegiatan');
                    $proposal->save();
                } catch (\Exception $e) {
                    Log::error('Error updating doc_berkegiatan_ketuplak: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Gagal memperbarui dokumen berkegiatan');
                }
            }

            if ($request->hasFile('doc_ormawa')) {
                try {
                    if ($proposal->doc_ormawa) {
                        $this->googleDrive->deleteFile($proposal->doc_ormawa);
                    }
                    $proposal->doc_ormawa = $this->googleDrive->uploadFile($request->file('doc_ormawa'), 'ormawa');
                    $proposal->save();
                } catch (\Exception $e) {
                    Log::error('Error updating doc_ormawa: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Gagal memperbarui dokumen ormawa');
                }
            }

            if ($request->hasFile('doc_sarana_prasarana')) {
                try {
                    if ($proposal->doc_sarana_prasarana) {
                        $this->googleDrive->deleteFile($proposal->doc_sarana_prasarana);
                    }
                    $proposal->doc_sarana_prasarana = $this->googleDrive->uploadFile($request->file('doc_sarana_prasarana'), 'sarana');
                    $proposal->save();
                } catch (\Exception $e) {
                    Log::error('Error updating doc_sarana_prasarana: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Gagal memperbarui dokumen sarana prasarana');
                }
            }

            return redirect()->back()->with('success', 'Dokumen berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error in update method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui dokumen: ' . $e->getMessage());
        }
    }

    public function destroy(Proposal $proposal)
    {
        try {
            // Delete associated files
            if ($proposal->doc_proposal && Storage::disk('public')->exists($proposal->doc_proposal)) {
                Storage::disk('public')->delete($proposal->doc_proposal);
            }
            if ($proposal->doc_berkegiatan_ketuplak && Storage::disk('public')->exists($proposal->doc_berkegiatan_ketuplak)) {
                Storage::disk('public')->delete($proposal->doc_berkegiatan_ketuplak);
            }
            if ($proposal->doc_ormawa && Storage::disk('public')->exists($proposal->doc_ormawa)) {
                Storage::disk('public')->delete($proposal->doc_ormawa);
            }
            if ($proposal->doc_sarana_prasarana && Storage::disk('public')->exists($proposal->doc_sarana_prasarana)) {
                Storage::disk('public')->delete($proposal->doc_sarana_prasarana);
            }
            if ($proposal->poster && Storage::disk('public')->exists($proposal->poster)) {
                Storage::disk('public')->delete($proposal->poster);
            }

            $proposal->delete();

            return redirect()->route('proposals.index')->with('success', 'Proposal berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus proposal: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Proposal $proposal)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $proposal->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Status proposal berhasil diperbarui');
    }

    public function approve(Request $request, Proposal $proposal)
    {
        if ($request->user()->role !== 'SEKERTARIS_KABINET') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $proposal->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $request->user()->id
            ]);

            return redirect()->back()->with('success', 'Proposal berhasil disetujui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyetujui proposal: ' . $e->getMessage());
        }
    }

    public function revise(Request $request, Proposal $proposal)
    {
        if ($request->user()->role !== 'SEKERTARIS_KABINET') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'revision_note' => 'required|string'
        ]);

        try {
            $proposal->update([
                'status' => 'revised',
                'revision_note' => $request->revision_note,
                'review_by' => $request->user()->id,
                'review_at' => now()
            ]);

            try {
                Mail::to($proposal->email)->send(new ProposalRevision($proposal));
            } catch (\Exception $e) {
                throw $e;
            }

            return redirect()->back()->with('success', 'Proposal berhasil direvisi dan notifikasi telah dikirim');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal merevisi proposal: ' . $e->getMessage());
        }
    }
}