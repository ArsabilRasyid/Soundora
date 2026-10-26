<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('albums', function (Blueprint $t) {
            $t->string('cover_path')->nullable();      // path storage ATAU URL sampul
            $t->unsignedSmallInteger('year')->nullable();
            $t->string('genre')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('albums', fn (Blueprint $t) => $t->dropColumn(['cover_path', 'year', 'genre']));
    }
};
