<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResidentialCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('residential_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Názov odberateľa
            $table->string('address'); // Sídlo
            $table->string('postal_code'); // PSČ
            $table->string('city'); // Mesto
            $table->string('ico'); // IČO
            $table->string('dic')->nullable(); // DIČ (nullable)
            $table->string('ic_dph')->nullable(); // IČ DPH (nullable)
            $table->string('iban')->nullable(); // IBAN (nullable)
            $table->string('bank_connection')->nullable(); // Bankové spojenie (nullable)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('residential_companies');
    }
}
