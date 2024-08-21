<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToResidentialCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('residential_companies', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade')->after('id');
            $table->string('ico')->nullable()->change();  // Nastavíme, že 'ico' môže byť null
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('residential_companies', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            $table->string('ico')->nullable(false)->change();  // Vrátime späť pôvodné nastavenie
        });
    }
}
