<?php

namespace App\Console\Commands;

use App\Mail\FirstReminderMail;
use App\Mail\SecondReminderMail;
use App\Models\Invoice;
use App\Services\MailService;
use Illuminate\Console\Command;

class SendInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automated email reminders for unpaid invoices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mailService = new MailService;
        $now = now();

        // Get all unpaid invoices
        $invoices = Invoice::where('placeno', false)
            ->with(['client', 'user'])
            ->get();

        $firstRemindersSent = 0;
        $secondRemindersSent = 0;

        foreach ($invoices as $invoice) {
            // Skip if client has no email
            if (! $invoice->client || ! $invoice->client->email) {
                continue;
            }

            // Skip if user has reminders disabled
            if (! $invoice->user->reminder_enabled) {
                continue;
            }

            $daysSinceIssued = $invoice->datum_izdavanja->diffInDays($now);
            $reminderInterval = $invoice->user->reminder_interval ?? 5;

            // Send second reminder after (interval * 2) days
            if ($daysSinceIssued >= ($reminderInterval * 2)) {
                if (! $mailService->hasEmailBeenSent($invoice, 'second_reminder')) {
                    try {
                        $mailService->sendInvoiceEmail($invoice, SecondReminderMail::class, 'second_reminder');
                        $secondRemindersSent++;
                        $this->info("Second reminder sent for invoice {$invoice->broj_fakture}");
                    } catch (\Exception $e) {
                        $this->error("Failed to send second reminder for invoice {$invoice->broj_fakture}: {$e->getMessage()}");
                    }
                }
            }
            // Send first reminder after interval days
            elseif ($daysSinceIssued >= $reminderInterval) {
                if (! $mailService->hasEmailBeenSent($invoice, 'first_reminder')) {
                    try {
                        $mailService->sendInvoiceEmail($invoice, FirstReminderMail::class, 'first_reminder');
                        $firstRemindersSent++;
                        $this->info("First reminder sent for invoice {$invoice->broj_fakture}");
                    } catch (\Exception $e) {
                        $this->error("Failed to send first reminder for invoice {$invoice->broj_fakture}: {$e->getMessage()}");
                    }
                }
            }
        }

        $this->info("Reminder sending completed: {$firstRemindersSent} first reminders, {$secondRemindersSent} second reminders sent.");

        return 0;
    }
}
