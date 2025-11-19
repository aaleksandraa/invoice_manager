<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('broj_fakture')->unique();
            $table->date('datum_izdavanja');
            $table->foreignId('klijent_id')->constrained('clients')->onDelete('cascade');
            $table->text('opis_posla');
            $table->integer('kolicina')->default(1);
            $table->decimal('cijena', 10, 2);
            $table->enum('valuta', ['BAM', 'EUR'])->default('BAM');
            $table->boolean('placeno')->default(false);
            $table->date('datum_placanja')->nullable();
            $table->decimal('uplaceni_iznos_eur', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
