<?php

namespace App\Mail;

use App\Models\Proposal;
use App\Models\AboutUs;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProposalRevision extends Mailable
{
    use Queueable, SerializesModels;

    public $proposal;
    public $aboutUs;

    public function __construct(Proposal $proposal)
    {
        $this->proposal = $proposal;
        $this->aboutUs = AboutUs::first(); // Mengambil data about us
    }

    public function build()
    {
        return $this->subject('Revisi Proposal: ' . $this->proposal->nama_kegiatan)
            ->view('emails.proposals.revision')
            ->with([
                'aboutUs' => $this->aboutUs
            ]);
    }
}