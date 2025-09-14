<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;
     
    public $customerName;
    public $invoiceHtml;
    /**
     * Create a new message instance.
     */
    public function __construct($customerName, $invoiceHtml)
    {
        $this->customerName = $customerName;
        $this->invoiceHtml = $invoiceHtml;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Mail',
        );
    }
        
    /**
     * summary:mail send views to return
     *
     * @return void
     */
    public function build()
    {
        return $this->subject("Invoice for your payments")
                    ->view('emails.wrapper')
                    ->with([
                        'invoiceHtml' => $this->invoiceHtml,
                    ]);
    }

}
