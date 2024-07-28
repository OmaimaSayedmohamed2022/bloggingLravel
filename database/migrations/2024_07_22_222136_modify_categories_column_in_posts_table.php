<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCategoriesColumnInPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->text('categories')->change(); // Change to text
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('categories')->change(); // Revert back if needed
        });
    }
}

