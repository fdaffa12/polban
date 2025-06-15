<?php

namespace App\Mail;

use App\Models\ProposalSpj;
use App\Models\AboutUs;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProposalSpjRevision extends Mailable
{
    use Queueable, SerializesModels;

    public $spj;
    public $aboutUs;

    public function __construct(ProposalSpj $spj)
    {
        $this->spj = $spj;
        $this->aboutUs = AboutUs::first(); // Mengambil data about us
    }

    public function build()
    {
        return $this->subject('Revisi SPJ: ' . $this->spj->proposal->nama_kegiatan)
            ->view('emails.proposals.spj-revision')
            ->with([
                'aboutUs' => $this->aboutUs
            ]);
    }
}