<?php

namespace App\Mail\CAK;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplianceSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $form,
        public string $type,
        public string $pdfPath
    ) {}

    public function build()
    {
        return $this->subject("{$this->type} Compliance Return - {$this->form->licensee_name}")
            ->view('emails.cak.compliance-submitted')
            ->attach($this->pdfPath, [
                'as' => "{$this->type}_Compliance_Return.pdf",
                'mime' => 'application/pdf',
            ]);
    }
}
