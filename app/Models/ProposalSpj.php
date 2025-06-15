<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalSpj extends Model
{
    protected $fillable = [
        'proposal_id',
        'doc_sptp',
        'doc_spj',
        'doc_berita_acara',
        'gambar_bukti_spj',
        'video',
        'caption_video'
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
}