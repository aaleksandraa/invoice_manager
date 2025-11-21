<?php

namespace App\Mail;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Opomena - Neplaćena faktura '.$this->invoice->broj_fakture,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment_reminder',
            with: [
                'invoice' => $this->invoice,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $this->invoice->load('client');
        $view = $this->invoice->valuta === 'BAM' ? 'invoices.invoice_bam' : 'invoices.invoice_eur';
        $pdf = Pdf::loadView($view, ['invoice' => $this->invoice])
            ->setPaper('a4', 'portrait');
        $safeFileName = str_replace('/', '-', $this->invoice->broj_fakture);

        return [
            Attachment::fromData(fn () => $pdf->output(), $safeFileName.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
