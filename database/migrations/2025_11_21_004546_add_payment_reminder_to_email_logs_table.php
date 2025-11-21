<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support altering enum columns, so we need to recreate the table
        if (Schema::hasTable('email_logs')) {
            // For SQLite (testing), we need to recreate the table
            if (DB::connection()->getDriverName() === 'sqlite') {
                // Rename old table
                Schema::rename('email_logs', 'email_logs_old');
                
                // Create new table with updated enum
                Schema::create('email_logs', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                    $table->string('recipient_email');
                    $table->enum('email_type', ['invoice', 'first_reminder', 'second_reminder', 'payment_reminder']);
                    $table->enum('status', ['sent', 'failed'])->default('sent');
                    $table->text('error_message')->nullable();
                    $table->timestamp('sent_at');
                    $table->timestamps();
                });
                
                // Copy data if old table exists and has data
                if (Schema::hasTable('email_logs_old')) {
                    DB::statement('INSERT INTO email_logs SELECT * FROM email_logs_old');
                    Schema::drop('email_logs_old');
                }
            } else {
                // For MySQL/PostgreSQL, use raw SQL to alter the enum
                DB::statement("ALTER TABLE email_logs MODIFY COLUMN email_type ENUM('invoice', 'first_reminder', 'second_reminder', 'payment_reminder')");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting is complex, so we'll just keep the new enum values
        // In production, you typically wouldn't roll back this kind of change
    }
};
