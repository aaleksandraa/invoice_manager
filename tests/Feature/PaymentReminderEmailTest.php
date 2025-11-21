<?php

namespace Tests\Feature;

use App\Mail\PaymentReminderMail;
use App\Models\Client;
use App\Models\EmailLog;
use App\Models\Invoice;
use App\Models\SmtpSetting;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PaymentReminderEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_reminder_email_cannot_be_sent_without_smtp_settings()
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

        $response = $this->actingAs($user)->post(route('invoices.send-email', $invoice));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('SMTP podešavanja', session('error'));
    }

    public function test_payment_reminder_email_cannot_be_sent_without_client_email()
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

        $response = $this->actingAs($user)->post(route('invoices.send-email', $invoice));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('email adresu', session('error'));
    }

    public function test_payment_reminder_email_logs_success_when_sent()
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
        $result = $mailService->sendInvoiceEmail($invoice, PaymentReminderMail::class, 'payment_reminder');

        $this->assertTrue($result);

        $this->assertDatabaseHas('email_logs', [
            'invoice_id' => $invoice->id,
            'user_id' => $user->id,
            'recipient_email' => 'client@example.com',
            'email_type' => 'payment_reminder',
            'status' => 'sent',
        ]);

        // Email is sent synchronously
        Mail::assertSent(PaymentReminderMail::class, function ($mail) use ($invoice) {
            return $mail->invoice->id === $invoice->id;
        });
    }

    public function test_payment_reminder_email_logs_failure_on_error()
    {
        $user = User::factory()->create();
        // No SMTP settings configured
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'email' => 'client@example.com',
        ]);
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'klijent_id' => $client->id,
        ]);

        $mailService = new MailService();

        try {
            $mailService->sendInvoiceEmail($invoice, PaymentReminderMail::class, 'payment_reminder');
            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            $this->assertStringContainsString('smtp', strtolower($e->getMessage()));
        }

        $this->assertDatabaseHas('email_logs', [
            'invoice_id' => $invoice->id,
            'user_id' => $user->id,
            'email_type' => 'payment_reminder',
            'status' => 'failed',
        ]);
    }

    public function test_payment_reminder_email_controller_shows_success_message()
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

        $response = $this->actingAs($user)->post(route('invoices.send-email', $invoice));

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertStringContainsString('client@example.com', session('success'));
    }

    public function test_user_cannot_send_email_for_other_users_invoice()
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

        $response = $this->actingAs($user1)->post(route('invoices.send-email', $invoice));

        $response->assertStatus(403);
    }
}
