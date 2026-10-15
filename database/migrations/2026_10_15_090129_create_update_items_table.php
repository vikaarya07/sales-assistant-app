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
        Schema::create('update_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('update_id')
                ->constrained('updates')
                ->cascadeOnDelete();

            $table->string('category', 30)->index();
            $table->string('title')->nullable();
            $table->text('description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_items');
    }
};
