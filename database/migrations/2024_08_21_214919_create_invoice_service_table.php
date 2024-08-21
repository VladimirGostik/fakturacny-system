<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceServiceTable extends Migration
{
    public function up()
    {
        Schema::create('invoice_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('service_description');  // Popis služby
            $table->decimal('service_price', 10, 2);  // Cena služby
            $table->string('place_name');  // Názov miesta v čase vytvorenia faktúry
            $table->text('place_header')->nullable();  // Popis do hlavičky v čase vytvorenia faktúry
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_service');
    }
}
