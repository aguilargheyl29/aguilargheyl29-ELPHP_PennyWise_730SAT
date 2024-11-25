<?php

// database/migrations/2024_11_25_100353_create_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id('categoryID'); // Primary key
            $table->string('categoryIcon')->nullable();
            $table->string('categoryName')->unique();
            $table->text('categoryDescription')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
