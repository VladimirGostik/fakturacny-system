<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AddDefaultUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert the user into the users table
        DB::table('users')->insert([
            'name' => 'Erika Keszegová',
            'email' => 'keszegova.diana@gmail.com',
            'password' => Hash::make('heslo123'), // Hashing the password
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optionally, you can delete the user in the down method
        DB::table('users')->where('email', 'keszegova.diana@gmail.com')->delete();
    }
}

