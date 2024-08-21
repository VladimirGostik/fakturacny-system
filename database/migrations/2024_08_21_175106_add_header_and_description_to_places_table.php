<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeaderAndDescriptionToPlacesTable extends Migration
{
    public function up()
    {
        Schema::table('places', function (Blueprint $table) {
            $table->text('header')->nullable(); // Popis do hlavičky
            $table->text('description')->nullable(); // Popis
        });
    }

    public function down()
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn('header');
            $table->dropColumn('description');
        });
    }
}
