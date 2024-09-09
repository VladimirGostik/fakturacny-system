<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInvoiceTypeInInvoicesTable extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Najprv dočasne odstránime starý enum 'invoice_type'
            $table->dropColumn('invoice_type');
        });

        Schema::table('invoices', function (Blueprint $table) {
            // Teraz pridáme nový enum s požadovanými hodnotami
            $table->enum('invoice_type', [
                'Hlavicka-Adresa-Nazov',
                'Nazov-Adresa-Hlavicka',
                'Hlavicka-Nazov-Adresa',
                'Nazov-Hlavicka-Adresa',
                'Adresa-Nazov-Hlavicka',
                'Adresa-Hlavicka-Nazov'
            ])->default('Hlavicka-Adresa-Nazov')->after('billing_month');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Pre rollback, znovu odstránime nový enum a pridáme starú verziu
            $table->dropColumn('invoice_type');
            $table->enum('invoice_type', [
                'Hlavicka-Adresa-Nazov', 
                'Hlavicka-Adresa-R', 
                'Hlavicka-Nazov-Adresa', 
                'N-Hlavicka-Adresa', 
                'Adresa-Nazov-Hlavicka', 
                'Adresa-Hlavicka-Nazov'
            ])->default('Hlavicka-Adresa-Nazov')->after('billing_month');
        });
    }
}
