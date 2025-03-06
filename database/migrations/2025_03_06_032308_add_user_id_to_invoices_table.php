<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToInvoicesTable extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Dodajemo user_id kao nullable jer postojeći podaci nemaju user_id
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->after('id');

            // Dodajemo složeni unique indeks za user_id i broj_fakture
            $table->unique(['user_id', 'broj_fakture']);
        });

        // Ažuriramo postojeće fakture da imaju user_id
        \App\Models\Invoice::all()->each(function ($invoice) {
            if ($invoice->client) {
                $invoice->user_id = $invoice->client->user_id;
                $invoice->save();
            }
        });

        // Nakon ažuriranja, možemo postaviti user_id kao NOT NULL
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'broj_fakture']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}