<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('company_profiles', 'pib_number')) {
                $table->string('pib_number', 50)->nullable()->after('tax_number');
            }
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string('pdv_broj')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('pdv_broj')->nullable(false)->change();
        });

        Schema::table('company_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('company_profiles', 'pib_number')) {
                $table->dropColumn('pib_number');
            }
        });
    }
};
