<?php

namespace Tests\Feature;

use App\Mail\FirstReminderMail;
use App\Mail\InvoiceMail;
use App\Mail\PaymentReminderMail;
use App\Mail\SecondReminderMail;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailPdfAttachmentTest extends TestCase
{
    use RefreshDatabase;

    private function createTestInvoice($valuta = 'BAM')
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'email' => 'client@example.com',
        ]);

        return Invoice::factory()->create([
            'user_id' => $user->id,
            'klijent_id' => $client->id,
            'valuta' => $valuta,
        ]);
    }

    public function test_invoice_mail_uses_bam_pdf_view_for_bam_invoices()
    {
        Mail::fake();

        $invoice = $this->createTestInvoice('BAM');
        $mailable = new InvoiceMail($invoice);

        // Get attachments
        $attachments = $mailable->attachments();

        $this->assertNotEmpty($attachments);
        $this->assertCount(1, $attachments);

        // Verify the attachment is a PDF
        $attachment = $attachments[0];
        $this->assertEquals('application/pdf', $attachment->mime);
    }

    public function test_invoice_mail_uses_eur_pdf_view_for_eur_invoices()
    {
        Mail::fake();

        $invoice = $this->createTestInvoice('EUR');
        $mailable = new InvoiceMail($invoice);

        // Get attachments
        $attachments = $mailable->attachments();

        $this->assertNotEmpty($attachments);
        $this->assertCount(1, $attachments);

        // Verify the attachment is a PDF
        $attachment = $attachments[0];
        $this->assertEquals('application/pdf', $attachment->mime);
    }

    public function test_payment_reminder_mail_uses_bam_pdf_view_for_bam_invoices()
    {
        Mail::fake();

        $invoice = $this->createTestInvoice('BAM');
        $mailable = new PaymentReminderMail($invoice);

        // Get attachments
        $attachments = $mailable->attachments();

        $this->assertNotEmpty($attachments);
        $this->assertCount(1, $attachments);

        // Verify the attachment is a PDF
        $attachment = $attachments[0];
        $this->assertEquals('application/pdf', $attachment->mime);
    }

    public function test_payment_reminder_mail_uses_eur_pdf_view_for_eur_invoices()
    {
        Mail::fake();

        $invoice = $this->createTestInvoice('EUR');
        $mailable = new PaymentReminderMail($invoice);

        // Get attachments
        $attachments = $mailable->attachments();

        $this->assertNotEmpty($attachments);
        $this->assertCount(1, $attachments);

        // Verify the attachment is a PDF
        $attachment = $attachments[0];
        $this->assertEquals('application/pdf', $attachment->mime);
    }

    public function test_first_reminder_mail_uses_bam_pdf_view_for_bam_invoices()
    {
        Mail::fake();

        $invoice = $this->createTestInvoice('BAM');
        $mailable = new FirstReminderMail($invoice);

        // Get attachments
        $attachments = $mailable->attachments();

        $this->assertNotEmpty($attachments);
        $this->assertCount(1, $attachments);

        // Verify the attachment is a PDF
        $attachment = $attachments[0];
        $this->assertEquals('application/pdf', $attachment->mime);
    }

    public function test_first_reminder_mail_uses_eur_pdf_view_for_eur_invoices()
    {
        Mail::fake();

        $invoice = $this->createTestInvoice('EUR');
        $mailable = new FirstReminderMail($invoice);

        // Get attachments
        $attachments = $mailable->attachments();

        $this->assertNotEmpty($attachments);
        $this->assertCount(1, $attachments);

        // Verify the attachment is a PDF
        $attachment = $attachments[0];
        $this->assertEquals('application/pdf', $attachment->mime);
    }

    public function test_second_reminder_mail_uses_bam_pdf_view_for_bam_invoices()
    {
        Mail::fake();

        $invoice = $this->createTestInvoice('BAM');
        $mailable = new SecondReminderMail($invoice);

        // Get attachments
        $attachments = $mailable->attachments();

        $this->assertNotEmpty($attachments);
        $this->assertCount(1, $attachments);

        // Verify the attachment is a PDF
        $attachment = $attachments[0];
        $this->assertEquals('application/pdf', $attachment->mime);
    }

    public function test_second_reminder_mail_uses_eur_pdf_view_for_eur_invoices()
    {
        Mail::fake();

        $invoice = $this->createTestInvoice('EUR');
        $mailable = new SecondReminderMail($invoice);

        // Get attachments
        $attachments = $mailable->attachments();

        $this->assertNotEmpty($attachments);
        $this->assertCount(1, $attachments);

        // Verify the attachment is a PDF
        $attachment = $attachments[0];
        $this->assertEquals('application/pdf', $attachment->mime);
    }
}
