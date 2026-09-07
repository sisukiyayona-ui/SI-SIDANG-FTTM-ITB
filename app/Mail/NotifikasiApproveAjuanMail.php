<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiApproveAjuanMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject("[APPROVE AJUAN SIDANG] {$this->data['tahapan']} - {$this->data['nama_mhs']} ({$this->data['nim']})")
            ->view('emails.notifikasi-approve-ajuan');
    }
}
