<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('description');
        });
    }

    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable();
            $table->text('description')->nullable();
        });
    }
};
