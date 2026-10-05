<?php

use App\Enums\MusicGenre;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('music', function (Blueprint $table) {
            $table
                ->string('genre')
                ->default(MusicGenre::OTHER->value)
                ->after('filename')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('music', function (Blueprint $table) {
            $table->dropColumn('genre');
        });
    }
};
