<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PengingatSidangMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $sidang;

    public function __construct(array $sidang)
    {
        $this->sidang = $sidang;
    }

    public function build()
    {
        return $this->subject("[PENGINGAT H-1] {$this->sidang['tahapan']} - {$this->sidang['nama_mhs']} ({$this->sidang['nim']})")
            ->view('emails.pengingat-sidang');
    }
}
