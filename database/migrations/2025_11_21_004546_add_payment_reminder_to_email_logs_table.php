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
                    // Only copy data with valid email_type values
                    DB::statement("INSERT INTO email_logs (id, invoice_id, user_id, recipient_email, email_type, status, error_message, sent_at, created_at, updated_at) 
                        SELECT id, invoice_id, user_id, recipient_email, email_type, status, error_message, sent_at, created_at, updated_at 
                        FROM email_logs_old 
                        WHERE email_type IN ('invoice', 'first_reminder', 'second_reminder')");
                    Schema::drop('email_logs_old');
                }
            } else {
                // For MySQL/PostgreSQL, we need different syntax
                $driver = DB::connection()->getDriverName();
                if ($driver === 'mysql') {
                    DB::statement("ALTER TABLE email_logs MODIFY COLUMN email_type ENUM('invoice', 'first_reminder', 'second_reminder', 'payment_reminder')");
                } elseif ($driver === 'pgsql') {
                    // PostgreSQL requires a different approach
                    DB::statement("ALTER TABLE email_logs ALTER COLUMN email_type TYPE VARCHAR(50)");
                    // Then add check constraint or use enum type if available
                }
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
