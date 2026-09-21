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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 30);
            $table->string('phone_normalized', 30)->unique();
            $table->string('name');
            $table->string('contract_number', 17);
            $table->unsignedBigInteger('amount');
            $table->string('branch');
            $table->string('source')->nullable();
            $table->string('status')->default('new')->index();
            $table->timestamp('last_contacted_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('name');
            $table->index('phone');
            $table->index('contract_number');
            $table->index('branch');
            $table->index('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
