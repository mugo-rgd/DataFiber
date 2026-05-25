<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExecutiveDashboardReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $data,
        public string $pdfPath
    ) {}

    public function build()
    {
        return $this->subject('Daily Executive Dashboard Report - ' . $this->data['snapshotDate'])
            ->view('emails.executive-dashboard-report')
            ->with($this->data)
            ->attach($this->pdfPath, [
                'as' => 'executive-dashboard-' . $this->data['snapshotDate'] . '.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
