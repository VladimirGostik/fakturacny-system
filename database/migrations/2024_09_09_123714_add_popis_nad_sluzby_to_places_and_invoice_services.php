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
        Schema::table('places', function (Blueprint $table) {
            $table->string('desc_above_service')->nullable()->after('header'); // nahraďte existing_column_name
        });

        Schema::table('invoice_services', function (Blueprint $table) {
            $table->string('desc_above_service')->nullable()->after('header'); // nahraďte existing_column_name
        });
    }

    public function down()
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn('desc_above_service');
        });

        Schema::table('invoice_services', function (Blueprint $table) {
            $table->dropColumn('desc_above_service');
        });
    }

};
