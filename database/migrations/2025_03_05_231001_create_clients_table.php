<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('naziv_firme');
            $table->string('adresa');
            $table->string('postanski_broj_mjesto_drzava');
            $table->string('pdv_broj');
            $table->string('email');
            $table->string('kontakt_telefon');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
