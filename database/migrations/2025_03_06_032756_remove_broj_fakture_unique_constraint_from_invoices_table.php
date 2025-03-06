<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveBrojFaktureUniqueConstraintFromInvoicesTable extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Uklanjamo staro ograničenje invoices_broj_fakture_unique
            $table->dropUnique('invoices_broj_fakture_unique');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Vraćamo ograničenje ako je potrebno u rollback-u
            $table->unique('broj_fakture', 'invoices_broj_fakture_unique');
        });
    }
}