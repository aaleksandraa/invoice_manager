<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\Invoice;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class MailService
{
    /**
     * Configure SMTP settings for a user
     */
    public function configureSmtp($userId)
    {
        $smtpSettings = SmtpSetting::where('user_id', $userId)->first();

        if (! $smtpSettings) {
            return false;
        }

        // Set mail configuration dynamically
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $smtpSettings->smtp_host);
        Config::set('mail.mailers.smtp.port', $smtpSettings->smtp_port);
        Config::set('mail.mailers.smtp.username', $smtpSettings->smtp_username);
        Config::set('mail.mailers.smtp.password', $smtpSettings->smtp_password);
        Config::set('mail.mailers.smtp.encryption', $smtpSettings->encryption === 'none' ? null : $smtpSettings->encryption);
        Config::set('mail.from.address', $smtpSettings->from_email);
        Config::set('mail.from.name', $smtpSettings->from_name);

        return true;
    }

    /**
     * Send an invoice email
     */
    public function sendInvoiceEmail(Invoice $invoice, $emailClass, $emailType)
    {
        try {
            // Configure SMTP for this user
            if (! $this->configureSmtp($invoice->user_id)) {
                \Log::error('Failed to send email: SMTP settings not configured', [
                    'invoice_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                ]);
                throw new \Exception('SMTP podešavanja nisu konfigurisana. Molimo konfigurirajte SMTP podešavanja u postavkama.');
            }

            // Get recipient email
            $recipientEmail = $invoice->client->email ?? null;
            if (! $recipientEmail) {
                \Log::error('Failed to send email: Client email not found', [
                    'invoice_id' => $invoice->id,
                    'client_id' => $invoice->client->id ?? null,
                ]);
                throw new \Exception('Email adresa klijenta nije pronađena. Molimo dodajte email adresu klijentu.');
            }

            \Log::info('Attempting to send email', [
                'invoice_id' => $invoice->id,
                'recipient_email' => $recipientEmail,
                'email_type' => $emailType,
            ]);

            // Send email
            Mail::to($recipientEmail)->send(new $emailClass($invoice));

            // Check if email was actually sent (no exception means success)
            \Log::info('Email sent successfully', [
                'invoice_id' => $invoice->id,
                'recipient_email' => $recipientEmail,
                'email_type' => $emailType,
            ]);

            // Log success
            EmailLog::create([
                'invoice_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'recipient_email' => $recipientEmail,
                'email_type' => $emailType,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send email', [
                'invoice_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'recipient_email' => $invoice->client->email ?? 'unknown',
                'email_type' => $emailType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Log failure
            EmailLog::create([
                'invoice_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'recipient_email' => $invoice->client->email ?? 'unknown',
                'email_type' => $emailType,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            throw $e;
        }
    }

    /**
     * Check if an email of a specific type has already been sent for an invoice
     */
    public function hasEmailBeenSent(Invoice $invoice, $emailType)
    {
        return EmailLog::where('invoice_id', $invoice->id)
            ->where('email_type', $emailType)
            ->where('status', 'sent')
            ->exists();
    }
}
