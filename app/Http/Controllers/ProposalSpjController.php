<?php

namespace App\Http\Controllers;

use App\Models\ProposalSpj;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\GoogleDriveService;
use Illuminate\Support\Facades\Log;

class ProposalSpjController extends Controller
{
    protected $googleDrive;

    public function __construct(GoogleDriveService $googleDrive)
    {
        $this->googleDrive = $googleDrive;
    }

    public function index(Proposal $proposal)
    {
        return Inertia::render('Proposals/Spj', [
            'proposal' => $proposal->load('spj'),
            'spjList' => ProposalSpj::where('proposal_id', $proposal->id)->get()
        ]);
    }

    public function store(Request $request, Proposal $proposal)
    {
        $request->validate([
            'doc_sptp' => 'required|mimes:pdf|max:10240',
            'doc_spj' => 'required|mimes:pdf|max:10240',
            'doc_berita_acara' => 'required|mimes:pdf|max:10240',
            'gambar_bukti_spj' => 'required|image|max:2048',
            'video' => 'required|mimes:mp4,mov,avi|max:102400',
            'caption_video' => 'required|string'
        ]);

        try {
            // Upload files ke Google Drive
            $docSptp = $this->googleDrive->uploadFile($request->file('doc_sptp'), 'sptp');
            $docSpj = $this->googleDrive->uploadFile($request->file('doc_spj'), 'spj');
            $docBeritaAcara = $this->googleDrive->uploadFile($request->file('doc_berita_acara'), 'berita_acara');
            $gambarBukti = $this->googleDrive->uploadFile($request->file('gambar_bukti_spj'), 'bukti_spj');
            $video = $this->googleDrive->uploadFile($request->file('video'), 'video_spj');

            ProposalSpj::create([
                'proposal_id' => $proposal->id,
                'doc_sptp' => $docSptp,
                'doc_spj' => $docSpj,
                'doc_berita_acara' => $docBeritaAcara,
                'gambar_bukti_spj' => $gambarBukti,
                'video' => $video,
                'caption_video' => $request->caption_video
            ]);

            return back()->with('success', 'SPJ berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error creating SPJ: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan SPJ: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ProposalSpj $spj)
    {
        $request->validate([
            'doc_sptp' => 'nullable|mimes:pdf|max:10240',
            'doc_spj' => 'nullable|mimes:pdf|max:10240',
            'doc_berita_acara' => 'nullable|mimes:pdf|max:10240',
            'gambar_bukti_spj' => 'nullable|image|max:2048',
            'video' => 'nullable|mimes:mp4,mov,avi|max:102400',
            'caption_video' => 'nullable|string'
        ]);

        try {
            $updateData = [];

            // Update files jika ada
            if ($request->hasFile('doc_sptp')) {
                $this->googleDrive->deleteFile($spj->doc_sptp);
                $updateData['doc_sptp'] = $this->googleDrive->uploadFile($request->file('doc_sptp'), 'sptp');
            }

            if ($request->hasFile('doc_spj')) {
                $this->googleDrive->deleteFile($spj->doc_spj);
                $updateData['doc_spj'] = $this->googleDrive->uploadFile($request->file('doc_spj'), 'spj');
            }

            if ($request->hasFile('doc_berita_acara')) {
                $this->googleDrive->deleteFile($spj->doc_berita_acara);
                $updateData['doc_berita_acara'] = $this->googleDrive->uploadFile($request->file('doc_berita_acara'), 'berita_acara');
            }

            if ($request->hasFile('gambar_bukti_spj')) {
                $this->googleDrive->deleteFile($spj->gambar_bukti_spj);
                $updateData['gambar_bukti_spj'] = $this->googleDrive->uploadFile($request->file('gambar_bukti_spj'), 'bukti_spj');
            }

            if ($request->hasFile('video')) {
                $this->googleDrive->deleteFile($spj->video);
                $updateData['video'] = $this->googleDrive->uploadFile($request->file('video'), 'video_spj');
            }

            if ($request->caption_video) {
                $updateData['caption_video'] = $request->caption_video;
            }

            $spj->update($updateData);

            return back()->with('success', 'SPJ berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating SPJ: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui SPJ: ' . $e->getMessage());
        }
    }

    public function destroy(ProposalSpj $spj)
    {
        try {
            // Delete files from Google Drive
            $this->googleDrive->deleteFile($spj->doc_sptp);
            $this->googleDrive->deleteFile($spj->doc_spj);
            $this->googleDrive->deleteFile($spj->doc_berita_acara);
            $this->googleDrive->deleteFile($spj->gambar_bukti_spj);
            $this->googleDrive->deleteFile($spj->video);

            $spj->delete();

            return back()->with('success', 'SPJ berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting SPJ: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus SPJ: ' . $e->getMessage());
        }
    }

    public function show(Proposal $proposal, ProposalSpj $spj)
    {
        return Inertia::render('Proposals/ShowSPJ', [
            'proposal' => $proposal,
            'spj' => $spj
        ]);
    }
}