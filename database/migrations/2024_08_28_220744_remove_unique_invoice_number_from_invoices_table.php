<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueInvoiceNumberFromInvoicesTable extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique(['invoice_number']); // Odstráni unikátny index
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unique('invoice_number'); // Obnoví unikátny index v prípade rollbacku
        });
    }
}
