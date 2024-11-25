<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetDataTable extends Migration
{
    public function up()
    {
        Schema::create('budget_data', function (Blueprint $table) {
            $table->id('budgetID'); // Primary key
            $table->unsignedBigInteger('userID'); // Foreign key to `users` table
            $table->unsignedBigInteger('categoryID'); // Foreign key to `categories` table
            $table->decimal('budgetLimit', 10, 2); // Required
            $table->text('budgetNotes')->nullable(); // Optional
            $table->timestamps(); // created_at and updated_at columns

            // Foreign key constraints
            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
            $table->foreign('categoryID')->references('categoryID')->on('categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('budget_data');
    }
}
