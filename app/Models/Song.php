<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Song extends Model
{
    protected $guarded = [];
    protected $casts = ['is_liked' => 'boolean'];

    public function artist() { return $this->belongsTo(Artist::class); }
    public function album() { return $this->belongsTo(Album::class); }

    // Relasi many-to-many dengan Playlist (tabel penghubung: playlist_song)
    public function playlists()
    {
        return $this->belongsToMany(Playlist::class)->withPivot('position');
    }

    public function isRemote(): bool
    {
        return Str::startsWith($this->audio_path, ['http://', 'https://']);
    }

    // Lagu hasil impor iTunes hanya berisi cuplikan 30 detik
    public function isPreview(): bool
    {
        return $this->isRemote() && Str::contains($this->audio_path, ['itunes.apple.com', 'mzstatic.com']);
    }

    public function getAudioUrlAttribute(): string
    {
        if (! $this->audio_path) return '';
        if ($this->isRemote()) return $this->audio_path;

        // Memastikan URL file audio lokal selalu valid diakses oleh browser
        return asset('storage/' . ltrim($this->audio_path, '/'));
    }

    // Nilai CSS "background": gambar sampul (file lokal atau URL) dengan gradasi sebagai cadangan
    public function getCoverCssAttribute(): string
    {
        return self::coverCss($this->cover_path, $this->id);
    }

    public static function coverCss(?string $path, int $id): string
    {
        if (! $path) return self::gradient($id);
        $url = Str::startsWith($path, ['http://', 'https://']) ? $path : asset('storage/' . ltrim($path, '/'));
        return "url('$url') center/cover, " . self::gradient($id);
    }

    public function getDurationLabelAttribute(): string
    {
        return $this->duration > 0
            ? intdiv($this->duration, 60) . ':' . str_pad($this->duration % 60, 2, '0', STR_PAD_LEFT)
            : '—';
    }

    public static function gradient(int $id): string
    {
        $h = ($id * 47 + 200) % 360;
        return "linear-gradient(135deg,hsl($h 70% 58%),hsl(" . (($h + 50) % 360) . " 65% 32%))";
    }
}