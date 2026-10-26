<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Lagu impor iTunes sebelumnya menyimpan durasi penuh padahal audionya cuplikan 30 detik
    public function up(): void
    {
        DB::table('songs')->where(fn ($q) => $q->where('audio_path', 'like', '%itunes.apple.com%')
            ->orWhere('audio_path', 'like', '%mzstatic.com%'))->update(['duration' => 30]);
    }

    public function down(): void {}
};
