<?php

namespace Tests\Feature;

use App\Mail\InvoiceMail;
use App\Models\Client;
use App\Models\EmailLog;
use App\Models\Invoice;
use App\Models\SmtpSetting;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvoiceEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_email_cannot_be_sent_without_smtp_settings()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'email' => 'client@example.com',
        ]);
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'klijent_id' => $client->id,
        ]);

        $response = $this->actingAs($user)->post(route('invoices.send-invoice', $invoice));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('SMTP podešavanja', session('error'));
    }

    public function test_invoice_email_cannot_be_sent_without_client_email()
    {
        $user = User::factory()->create();
        SmtpSetting::factory()->create(['user_id' => $user->id]);
        $client = Client::factory()->withoutEmail()->create([
            'user_id' => $user->id,
        ]);
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'klijent_id' => $client->id,
        ]);

        $response = $this->actingAs($user)->post(route('invoices.send-invoice', $invoice));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('email adresu', session('error'));
    }

    public function test_invoice_email_logs_success_when_sent()
    {
        Mail::fake();

        $user = User::factory()->create();
        SmtpSetting::factory()->create(['user_id' => $user->id]);
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'email' => 'client@example.com',
        ]);
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'klijent_id' => $client->id,
        ]);

        $mailService = new MailService();
        $result = $mailService->sendInvoiceEmail($invoice, InvoiceMail::class, 'invoice');

        $this->assertTrue($result);

        $this->assertDatabaseHas('email_logs', [
            'invoice_id' => $invoice->id,
            'user_id' => $user->id,
            'recipient_email' => 'client@example.com',
            'email_type' => 'invoice',
            'status' => 'sent',
        ]);

        // Email is sent synchronously
        Mail::assertSent(InvoiceMail::class, function ($mail) use ($invoice) {
            return $mail->invoice->id === $invoice->id;
        });
    }

    public function test_invoice_email_controller_shows_success_message()
    {
        Mail::fake();

        $user = User::factory()->create();
        SmtpSetting::factory()->create(['user_id' => $user->id]);
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'email' => 'client@example.com',
        ]);
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'klijent_id' => $client->id,
        ]);

        $response = $this->actingAs($user)->post(route('invoices.send-invoice', $invoice));

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertStringContainsString('Faktura je uspješno poslana', session('success'));
        $this->assertStringContainsString('client@example.com', session('success'));
    }

    public function test_user_cannot_send_invoice_for_other_users_invoice()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $client = Client::factory()->create([
            'user_id' => $user2->id,
            'email' => 'client@example.com',
        ]);
        $invoice = Invoice::factory()->create([
            'user_id' => $user2->id,
            'klijent_id' => $client->id,
        ]);

        $response = $this->actingAs($user1)->post(route('invoices.send-invoice', $invoice));

        $response->assertStatus(403);
    }

    public function test_invoice_and_reminder_routes_work_independently()
    {
        Mail::fake();

        $user = User::factory()->create();
        SmtpSetting::factory()->create(['user_id' => $user->id]);
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'email' => 'client@example.com',
        ]);
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'klijent_id' => $client->id,
        ]);

        // Test send-invoice route
        $response1 = $this->actingAs($user)->post(route('invoices.send-invoice', $invoice));
        $response1->assertStatus(302);
        $response1->assertSessionHas('success');

        // Test send-email (reminder) route
        $response2 = $this->actingAs($user)->post(route('invoices.send-email', $invoice));
        $response2->assertStatus(302);
        $response2->assertSessionHas('success');

        // Verify both emails were sent
        Mail::assertSent(InvoiceMail::class, 1);
        Mail::assertSent(\App\Mail\PaymentReminderMail::class, 1);

        // Verify database logs
        $this->assertDatabaseHas('email_logs', [
            'invoice_id' => $invoice->id,
            'email_type' => 'invoice',
            'status' => 'sent',
        ]);
        $this->assertDatabaseHas('email_logs', [
            'invoice_id' => $invoice->id,
            'email_type' => 'payment_reminder',
            'status' => 'sent',
        ]);
    }
}
