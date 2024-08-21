<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Názov firmy
            $table->string('address'); // Sídlo firmy
            $table->string('postal_code'); // PSČ
            $table->string('city'); // Mesto
            $table->string('ico'); // IČO firmy
            $table->string('dic'); // DIČ firmy
            $table->string('iban'); // IBAN firmy
            $table->string('bank_connection', 10); // Bankové spojenie (max. 10 znakov)
            $table->timestamps(); // created_at a updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
