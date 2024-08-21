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
            $table->string('invoice_number')->unique();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');  // ID firmy
            $table->foreignId('residential_company_id')->constrained('residential_companies')->onDelete('cascade')->nullable();  // ID bytového podniku
            $table->string('residential_company_name')->nullable();  // Názov bytového podniku
            $table->string('residential_company_address')->nullable();  // Adresa bytového podniku
            $table->string('residential_company_city')->nullable();  // Mesto bytového podniku
            $table->string('residential_company_postal_code')->nullable();  // PSČ bytového podniku
            $table->string('residential_company_ico')->nullable();  // IČO bytového podniku
            $table->string('residential_company_dic')->nullable();  // DIČ bytového podniku
            $table->string('residential_company_ic_dph')->nullable();  // IČ DPH bytového podniku
            $table->string('residential_company_iban')->nullable();  // IBAN bytového podniku
            $table->string('residential_company_bank_connection')->nullable();  // Bankové spojenie bytového podniku
            $table->date('issue_date');  // Dátum vytvorenia
            $table->date('due_date');  // Dátum splatnosti
            $table->enum('status', ['created', 'sent', 'expired', 'paid'])->default('created');  // Aktuálny stav faktúry
            $table->string('billing_month');  // Mesiac za ktorý sa fakturuje
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
