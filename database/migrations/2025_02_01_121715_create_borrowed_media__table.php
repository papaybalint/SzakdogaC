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
        Schema::create('borrowed_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowings_id')->constrained()->onDelete('cascade');
            $table->foreignId('items_id')->constrained()->onDelete('cascade');
            $table->date('returned_date')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowed_media_');
    }
};
