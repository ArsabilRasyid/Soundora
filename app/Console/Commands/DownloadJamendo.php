<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Unduh lagu Creative Commons dari Jamendo ke folder musik, rapi per Artis/Album.
 * Hanya lagu yang diizinkan diunduh oleh pemiliknya (audiodownload_allowed).
 */
class DownloadJamendo extends Command
{
    protected $signature = 'soundora:jamendo-download
        {path=D:/Musik : Folder tujuan}
        {--tag=* : Genre/tag, mis. --tag=funk}
        {--limit=30 : Maks. lagu per tag}
        {--client= : Client ID Jamendo (atau JAMENDO_CLIENT_ID di .env)}';

    protected $description = 'Unduh lagu Jamendo ke folder musik (Artis/Album/NN - Judul.mp3)';

    public function handle(): int
    {
        $client = $this->option('client') ?: env('JAMENDO_CLIENT_ID');
        if (! $client) { $this->error('Isi JAMENDO_CLIENT_ID di .env (daftar gratis di devportal.jamendo.com).'); return self::FAILURE; }

        $root = rtrim($this->argument('path'), '/\\');
        $total = 0;

        foreach ($this->option('tag') ?: ['funk', 'lofi', 'pop'] as $tag) {
            $res = Http::timeout(25)->retry(2, 2000)->get('https://api.jamendo.com/v3.0/tracks/', [
                'client_id' => $client, 'format' => 'json', 'limit' => min((int) $this->option('limit'), 200),
                'tags' => $tag, 'track_type' => 'albumtrack', 'order' => 'popularity_month', 'audioformat' => 'mp32',
            ])->json();

            if (($res['headers']['status'] ?? '') !== 'success') {
                $this->error("Jamendo menolak “$tag”: " . ($res['headers']['error_message'] ?? '?')); continue;
            }

            foreach ($res['results'] ?? [] as $r) {
                if (empty($r['audiodownload']) || (array_key_exists('audiodownload_allowed', $r) && ! $r['audiodownload_allowed'])) continue;

                $dir = $root . '/' . $this->clean($r['artist_name']) . '/' . $this->clean($r['album_name'] ?? 'Single');
                if (! is_dir($dir)) mkdir($dir, 0777, true);

                $file = $dir . '/' . sprintf('%02d - %s.mp3', (int) ($r['position'] ?? 0), $this->clean($r['name']));
                if (is_file($file)) continue;

                $resp = Http::timeout(300)->sink($file)->get($r['audiodownload']);
                if (! $resp->successful()) { @unlink($file); $this->warn('Gagal: ' . $r['name']); continue; }

                $cover = $dir . '/cover.jpg';
                if (! is_file($cover) && ! empty($r['album_image'])) {
                    try { file_put_contents($cover, Http::timeout(30)->get($r['album_image'])->body()); } catch (\Throwable) {}
                }

                $total++;
                $this->line('✓ ' . $r['artist_name'] . ' / ' . ($r['album_name'] ?? 'Single') . ' / ' . $r['name']);
            }
        }

        $this->info("Selesai: $total lagu diunduh ke $root. Lanjutkan: php artisan soundora:scan \"$root\"");
        return self::SUCCESS;
    }

    private function clean(string $s): string
    {
        $s = trim(preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $s));
        return Str::limit($s !== '' ? $s : 'Tanpa Nama', 80, '');
    }
}
