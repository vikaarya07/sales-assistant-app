<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_statuses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status', 32);

            $table->unique(['customer_id', 'status']);
        });

        // Pindahkan status yang sudah dimiliki setiap customer.
        DB::table('customers')
            ->select('id', 'status')
            ->whereNotNull('status')
            ->orderBy('id')
            ->chunkById(500, function ($customers): void {
                $rows = [];

                foreach ($customers as $customer) {
                    $rows[] = [
                        'customer_id' => $customer->id,
                        'status' => $customer->status,
                    ];
                }

                if ($rows !== []) {
                    DB::table('customer_statuses')->insertOrIgnore($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_statuses');
    }
};
