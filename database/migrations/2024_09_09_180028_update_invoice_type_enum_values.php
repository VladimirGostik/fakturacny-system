<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInvoiceTypeEnumValues extends Migration
{
    public function up()
    {
        // 1. Pridaj dočasný stĺpec s novými hodnotami
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('new_invoice_type', [
                'Hlavicka-Adresa-Nazov',
                'Nazov-Adresa-Hlavicka',
                'Hlavicka-Nazov-Adresa',
                'Nazov-Hlavicka-Adresa',
                'Adresa-Nazov-Hlavicka',
                'Adresa-Hlavicka-Nazov'
            ])->default('Hlavicka-Adresa-Nazov')->after('billing_month');
        });

        // 2. Skopíruj údaje zo starého stĺpca do nového
        DB::statement('UPDATE invoices SET new_invoice_type = invoice_type');

        // 3. Odstráň starý stĺpec
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_type');
        });

        // 4. Premenuj nový stĺpec na pôvodný názov
        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('new_invoice_type', 'invoice_type');
        });
    }

    public function down()
    {
        // V prípade rollbacku vykonaj opačný postup
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('new_invoice_type', [
                'Hlavicka-Adresa-Nazov', 'Hlavicka-Adresa-R', 'Hlavicka-Nazov-Adresa', 
                'N-Hlavicka-Adresa', 'Adresa-Nazov-Hlavicka', 'Adresa-Hlavicka-Nazov'
            ])->default('Hlavicka-Adresa-Nazov')->after('billing_month');
        });

        DB::statement('UPDATE invoices SET new_invoice_type = invoice_type');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_type');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('new_invoice_type', 'invoice_type');
        });
    }
}
