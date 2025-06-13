<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    protected $fillable = [
        'pic_name',
        'email',
        'phone',
        'nama_kegiatan',
        'bidang_kegiatan',
        'jenis_kegiatan',
        'department_id',
        'tanggal_mulai',
        'tanggal_akhir',
        'tempat_kegiatan',
        'jumlah_peserta',
        'jumlah_panitia',
        'jumlah_spj',
        'dana_dipa_polban',
        'dana_swadaya',
        'dana_sponsor',
        'pengisi_acara',
        'sponsorship',
        'media_partner',
        'doc_proposal',
        'doc_berkegiatan_ketuplak',
        'doc_ormawa',
        'doc_sarana_prasarana',
        'link_surat_izin_ortu',
        'poster',
        'caption_poster',
        'status',
        'revision_note',
        'approved_at',
        'approved_by',
        'revised_by',
        'review_by',
        'review_at',
    ];

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function revisedBy()
    {
        return $this->belongsTo(User::class, 'revised_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'review_by');
    }

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
        'dana_dipa_polban' => 'decimal:2',
        'dana_swadaya' => 'decimal:2',
        'dana_sponsor' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relasi untuk reviewer
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'review_by');
    }

    // Relasi untuk approved by
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}