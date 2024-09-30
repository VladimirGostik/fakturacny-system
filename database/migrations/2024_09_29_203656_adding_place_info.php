<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->string('residential_company_name')->nullable()->after('residential_company_id'); // nahraďte existing_column_name
            $table->string('residential_company_address')->nullable()->after('residential_company_name');
            $table->string('residential_company_city')->nullable()->after('residential_company_address');
            $table->string('residential_company_postal_code')->nullable()->after('residential_company_city');
            $table->string('residential_company_ico')->nullable()->after('residential_company_postal_code');
            $table->string('residential_company_dic')->nullable()->after('residential_company_ico');
            $table->string('residential_company_ic_dph')->nullable()->after('residential_company_dic');
            $table->string('residential_company_iban')->nullable()->after('residential_company_ic_dph');
            $table->string('residential_company_bank_connection')->nullable()->after('residential_company_iban');
            $table->enum('invoice_type', [
                'Hlavicka-Adresa-Nazov',
                'Nazov-Adresa-Hlavicka',
                'Hlavicka-Nazov-Adresa',
                'Nazov-Hlavicka-Adresa',
                'Adresa-Nazov-Hlavicka',
                'Adresa-Hlavicka-Nazov'
            ])->default('Hlavicka-Adresa-Nazov')->after('residential_company_bank_connection'); 
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn([
                'residential_company_name',
                'residential_company_address',
                'residential_company_city',
                'residential_company_postal_code',
                'residential_company_ico',
                'residential_company_dic',
                'residential_company_ic_dph',
                'residential_company_iban',
                'residential_company_bank_connection',
                'invoice_type',
            ]);
        });
    }
};
