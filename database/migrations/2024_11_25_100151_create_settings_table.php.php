<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id('settingsID'); // Primary key
            $table->unsignedBigInteger('userID'); // Foreign key to `users` table
            $table->unsignedBigInteger('categoryID'); // Foreign key to `categories` table
            $table->decimal('budgetPerCategory', 10, 2)->nullable(); // Optional
            $table->decimal('budgetPerExpense', 10, 2)->nullable(); // Optional
            $table->timestamps(); // created_at and updated_at columns

            // Foreign key constraints
            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
            $table->foreign('categoryID')->references('categoryID')->on('categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
