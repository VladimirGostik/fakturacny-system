<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('invoice_type', [
                'Hlavicka-Adresa-Nazov', 'Hlavicka-Adresa-R', 'Hlavicka-Nazov-Adresa', 'N-Hlavicka-Adresa', 'Adresa-Nazov-Hlavicka', 'Adresa-Hlavicka-Nazov'
            ])->default('Hlavicka-Adresa-Nazov')->after('billing_month'); 
        });
    }
    
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_type');
        });
    }
    
};
