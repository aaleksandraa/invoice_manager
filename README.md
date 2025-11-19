# Invoice Manager

Invoice management system built with Laravel.

## Features

- Invoice creation and management
- Client management
- Payment tracking
- Automated email reminders for unpaid invoices
- PDF invoice generation
- SMTP email configuration

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Set up environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Run migrations:
   ```bash
   php artisan migrate
   ```

5. Build assets:
   ```bash
   npm run build
   ```

## Email Reminder System

The application includes an automated email reminder system for unpaid invoices:

- **First reminder**: Sent automatically 5 days after invoice date
- **Second reminder (Opomena)**: Sent automatically 10 days after invoice date

### Setting up Automated Reminders

To enable automated email reminders, you need to set up Laravel's task scheduler:

#### On Linux/Mac (Cron Job)

Add the following cron entry to your crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

To edit your crontab:
```bash
crontab -e
```

#### On Windows (Task Scheduler)

Create a scheduled task that runs the following command every minute:

```bash
php /path-to-your-project/artisan schedule:run
```

### SMTP Configuration

Before sending emails, configure your SMTP settings:

1. Navigate to **Settings** in the application
2. Fill in the **SMTP Settings** section:
   - SMTP Server/Host
   - SMTP Port (default: 587)
   - SMTP Username
   - SMTP Password
   - From Email Address
   - From Name
   - Encryption Type (TLS/SSL/None)
3. Click "Save SMTP Settings"

### Manual Email Sending

To manually send an invoice to a client:

1. Open the invoice details page
2. Click the "Pošalji" (Send) button
3. The invoice will be sent immediately with a PDF attachment

### Testing the Reminder Command

You can manually trigger the reminder check by running:

```bash
php artisan invoices:send-reminders
```

This command will:
- Check all unpaid invoices
- Send first reminders for invoices 5+ days old (if not already sent)
- Send second reminders for invoices 10+ days old (if not already sent)

## Queue Configuration

The email sending is queued for better performance. Make sure to run the queue worker:

```bash
php artisan queue:work
```

For production, you should set up a process manager like Supervisor to keep the queue worker running.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
