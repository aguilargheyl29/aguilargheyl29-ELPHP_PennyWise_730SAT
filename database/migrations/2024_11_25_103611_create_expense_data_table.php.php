<?php

// database/migrations/2024_11_25_100429_create_expense_data_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseDataTable extends Migration
{
    public function up()
    {
        Schema::create('expense_data', function (Blueprint $table) {
            $table->id('expenseID'); // Primary key
            $table->unsignedBigInteger('userID'); // Foreign key referencing 'userID' in 'users'
            $table->unsignedBigInteger('categoryID'); // Foreign key referencing 'categoryID' in 'categories'
            $table->string('expenseName');
            $table->decimal('expenseAmount', 10, 2);
            $table->text('expenseDescription')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
            $table->foreign('categoryID')->references('categoryID')->on('categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('expense_data');
    }
}
