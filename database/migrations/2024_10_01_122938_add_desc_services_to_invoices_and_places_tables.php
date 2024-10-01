<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Pridanie stĺpca desc_services do tabulky invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('desc_services')->nullable(); // alebo $table->string('desc_services')->nullable();
        });

        // Pridanie stĺpca desc_services do tabulky places
        Schema::table('places', function (Blueprint $table) {
            $table->text('desc_services')->nullable(); // alebo $table->string('desc_services')->nullable();
        });
    }

    public function down()
    {
        // Odstránenie stĺpca desc_services z tabulky invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('desc_services');
        });

        // Odstránenie stĺpca desc_services z tabulky places
        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn('desc_services');
        });
    }
};
