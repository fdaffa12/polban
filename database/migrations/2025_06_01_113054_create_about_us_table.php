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
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            $table->string('au_title');
            $table->text('au_desc');
            $table->string('au_image')->nullable();
            $table->json('au_multiple_image')->nullable();
            $table->json('au_values')->nullable(); // For storing company values
            $table->json('history')->nullable(); // For storing company history
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('about_us');
    }
};
