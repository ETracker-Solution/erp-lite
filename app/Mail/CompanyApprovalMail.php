<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $company;
    protected $credentials;

    /**
     * Create a new message instance.
     */
    public function __construct($company, $credentials)
    {
        $this->company = $company;
        $this->credentials = $credentials;

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        return new Envelope(
            subject: 'Company Approval Mail',
            
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.approval_notification',
            with: [
                'company' => $this->company,
                'credentials' => $this->credentials
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
