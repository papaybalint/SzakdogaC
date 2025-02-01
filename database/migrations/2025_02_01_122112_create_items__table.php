<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items_', function (Blueprint $table) {
            $table->id();
            $table->string('author');
            $table->string('title');
            $table->string('invertory_number');
            $table->integer('barcode');
            $table->string('isbn');
            $table->date('year_of_purchasing');
            $table->integer('published_year');
            $table->string('supplier');
            $table->foreignId('categories_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_');
    }
};
