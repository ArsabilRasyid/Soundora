<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artists', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->timestamps();
        });

        Schema::create('albums', function (Blueprint $t) {
            $t->id();
            $t->foreignId('artist_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->timestamps();
        });

        Schema::create('songs', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->foreignId('artist_id')->constrained()->cascadeOnDelete();
            $t->foreignId('album_id')->nullable()->constrained()->nullOnDelete();
            $t->string('audio_path');               // path di disk public ATAU URL penuh
            $t->string('cover_path')->nullable();
            $t->unsignedInteger('duration')->default(0); // detik
            $t->boolean('is_liked')->default(false);
            $t->timestamps();
        });

        Schema::create('playlists', function (Blueprint $t) {
            $t->id();
            $t->string('name', 60);
            $t->string('description')->nullable();
            $t->timestamps();
        });

        // Tabel penghubung many-to-many (urutan lagu disimpan di kolom position)
        Schema::create('playlist_song', function (Blueprint $t) {
            $t->id();
            $t->foreignId('playlist_id')->constrained()->cascadeOnDelete();
            $t->foreignId('song_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('position')->default(0);
            $t->timestamps();
            $t->unique(['playlist_id', 'song_id']); // lagu yang sama tidak bisa dobel di satu playlist
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlist_song');
        Schema::dropIfExists('playlists');
        Schema::dropIfExists('songs');
        Schema::dropIfExists('albums');
        Schema::dropIfExists('artists');
    }
};
