<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// class CreateUsersTable extends Migration
// {
//     public function up()
//     {
//         Schema::create('users', function (Blueprint $table) {
//             $table->id('userID');
//             $table->string('username')->nullable();
//             $table->string('userEmail')->unique();
//             $table->string('userPassword');
//             $table->string('userFullName')->nullable();
//             $table->string('userImage')->nullable();
//             $table->rememberToken();
//             $table->timestamps();
//         });
//     }

//     public function down()
//     {
//         Schema::dropIfExists('users');
//     }
// }


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('userID');
            $table->string('userEmail')->unique();
            $table->string('userPassword');
            $table->string('username')->nullable();
            $table->string('userFullName')->nullable();
            $table->string('userImage')->nullable();
            $table->timestamp('email_verified_at')->nullable(); // Updated column
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
