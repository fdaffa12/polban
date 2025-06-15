<?php

namespace App\Http\Controllers;

use App\Models\ProposalSpj;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\GoogleDriveService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProposalSpjRevision;

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
        try {
            // Validasi file yang diupload
            $request->validate([
                'doc_sptp' => 'nullable|mimes:pdf|max:10240',
                'doc_spj' => 'nullable|mimes:pdf|max:10240',
                'doc_berita_acara' => 'nullable|mimes:pdf|max:10240',
                'gambar_bukti_spj' => 'nullable|image|max:2048',
                'video' => 'nullable|mimes:mp4,mov,avi|max:102400',
                'caption_video' => 'nullable|string'
            ]);

            // Update file yang diupload
            if ($request->hasFile('doc_sptp')) {
                try {
                    if ($spj->doc_sptp) {
                        $this->googleDrive->deleteFile($spj->doc_sptp);
                    }
                    $spj->doc_sptp = $this->googleDrive->uploadFile($request->file('doc_sptp'), 'sptp');
                    $spj->save();
                } catch (\Exception $e) {
                    Log::error('Error updating doc_sptp: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Gagal memperbarui dokumen SPTP');
                }
            }

            if ($request->hasFile('doc_spj')) {
                try {
                    if ($spj->doc_spj) {
                        $this->googleDrive->deleteFile($spj->doc_spj);
                    }
                    $spj->doc_spj = $this->googleDrive->uploadFile($request->file('doc_spj'), 'spj');
                    $spj->save();
                } catch (\Exception $e) {
                    Log::error('Error updating doc_spj: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Gagal memperbarui dokumen SPJ');
                }
            }

            if ($request->hasFile('doc_berita_acara')) {
                try {
                    if ($spj->doc_berita_acara) {
                        $this->googleDrive->deleteFile($spj->doc_berita_acara);
                    }
                    $spj->doc_berita_acara = $this->googleDrive->uploadFile($request->file('doc_berita_acara'), 'berita_acara');
                    $spj->save();
                } catch (\Exception $e) {
                    Log::error('Error updating doc_berita_acara: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Gagal memperbarui dokumen berita acara');
                }
            }

            if ($request->hasFile('gambar_bukti_spj')) {
                try {
                    if ($spj->gambar_bukti_spj) {
                        $this->googleDrive->deleteFile($spj->gambar_bukti_spj);
                    }
                    $spj->gambar_bukti_spj = $this->googleDrive->uploadFile($request->file('gambar_bukti_spj'), 'bukti_spj');
                    $spj->save();
                } catch (\Exception $e) {
                    Log::error('Error updating gambar_bukti_spj: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Gagal memperbarui gambar bukti SPJ');
                }
            }

            if ($request->hasFile('video')) {
                try {
                    if ($spj->video) {
                        $this->googleDrive->deleteFile($spj->video);
                    }
                    $spj->video = $this->googleDrive->uploadFile($request->file('video'), 'video_spj');
                    $spj->save();
                } catch (\Exception $e) {
                    Log::error('Error updating video: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Gagal memperbarui video');
                }
            }

            if ($request->caption_video) {
                $spj->caption_video = $request->caption_video;
                $spj->save();
            }

            return redirect()->back()->with('success', 'Dokumen berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error in update method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui dokumen: ' . $e->getMessage());
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

    public function approve(Request $request, ProposalSpj $spj)
    {
        if ($request->user()->role !== 'SEKERTARIS_KABINET') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $spj->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $request->user()->id
            ]);

            return redirect()->back()->with('success', 'SPJ berhasil disetujui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyetujui SPJ: ' . $e->getMessage());
        }
    }

    public function revise(Request $request, ProposalSpj $spj)
    {
        if ($request->user()->role !== 'SEKERTARIS_KABINET') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'revision_note' => 'required|string'
        ]);

        try {
            $spj->update([
                'status' => 'revised',
                'revision_note' => $request->revision_note,
                'review_by' => $request->user()->id,
                'review_at' => now()
            ]);

            // Kirim email notifikasi
            Mail::to($spj->proposal->email)->send(new ProposalSpjRevision($spj));

            return redirect()->back()->with('success', 'SPJ berhasil direvisi dan notifikasi telah dikirim');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal merevisi SPJ: ' . $e->getMessage());
        }
    }
}