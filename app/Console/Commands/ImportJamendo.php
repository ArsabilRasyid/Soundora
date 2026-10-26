<?php

namespace App\Console\Commands;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Impor lagu DURASI PENUH yang legal dari Jamendo (musik Creative Commons dari artis independen).
 * Daftar gratis untuk mendapat Client ID: https://devportal.jamendo.com
 */
class ImportJamendo extends Command
{
    protected $signature = 'soundora:jamendo
        {--tag=* : Genre/tag, mis. --tag=funk --tag=pop}
        {--limit=50 : Maks. lagu per tag (maks 200)}
        {--client= : Client ID Jamendo (atau isi JAMENDO_CLIENT_ID di .env)}
        {--clear-previews : Hapus dulu lagu cuplikan 30 detik (iTunes) dan lagu contoh SoundHelix}';

    protected $description = 'Impor lagu durasi penuh (Creative Commons) dari Jamendo';

    private const TAGS = ['funk', 'pop', 'rock', 'hiphop', 'rnb', 'soul', 'lofi', 'jazz', 'electronic', 'indie', 'reggae', 'acoustic'];

    public function handle(): int
    {
        $client = $this->option('client') ?: env('JAMENDO_CLIENT_ID');
        if (! $client) {
            $this->error('Client ID belum ada. Daftar gratis di https://devportal.jamendo.com lalu isi JAMENDO_CLIENT_ID=... di file .env');
            return self::FAILURE;
        }

        if ($this->option('clear-previews')) {
            Song::where(fn ($q) => $q->where('audio_path', 'like', '%itunes.apple.com%')
                ->orWhere('audio_path', 'like', '%mzstatic.com%')
                ->orWhere('audio_path', 'like', '%soundhelix%'))->delete();
            Album::doesntHave('songs')->delete();
            Artist::doesntHave('songs')->delete();
            $this->info('Lagu cuplikan dan lagu contoh dihapus.');
        }

        foreach ($this->option('tag') ?: self::TAGS as $tag) {
            $this->import($tag, $client);
            sleep(1);
        }

        $this->info('Selesai. Total: ' . Song::count() . ' lagu, ' . Album::count() . ' album, ' . Artist::count() . ' artis.');
        return self::SUCCESS;
    }

    private function import(string $tag, string $client): void
    {
        try {
            $res = Http::timeout(25)->retry(2, 2000)->get('https://api.jamendo.com/v3.0/tracks/', [
                'client_id'   => $client,
                'format'      => 'json',
                'limit'       => min((int) $this->option('limit'), 200),
                'tags'        => $tag,
                'track_type'  => 'albumtrack',     // hanya lagu yang punya album
                'order'       => 'popularity_month',
                'audioformat' => 'mp32',           // MP3 VBR kualitas bagus, durasi penuh
            ])->json();
        } catch (\Throwable $e) {
            $this->error("Gagal mengambil “$tag”: " . $e->getMessage());
            return;
        }

        if (($res['headers']['status'] ?? '') !== 'success') {
            $this->error("Jamendo menolak permintaan “$tag”: " . ($res['headers']['error_message'] ?? 'tidak diketahui'));
            return;
        }

        $added = 0;
        foreach ($res['results'] ?? [] as $r) {
            if (empty($r['audio']) || empty($r['name']) || empty($r['artist_name'])) continue;

            $cover = $r['album_image'] ?: ($r['image'] ?? null);
            $artist = Artist::firstOrCreate(['name' => $r['artist_name']]);
            $album = ! empty($r['album_name'])
                ? Album::firstOrCreate(
                    ['artist_id' => $artist->id, 'name' => $r['album_name']],
                    ['cover_path' => $cover, 'genre' => $tag,
                     'year' => isset($r['releasedate']) ? (int) substr($r['releasedate'], 0, 4) : null])
                : null;

            $song = Song::firstOrCreate(
                ['artist_id' => $artist->id, 'album_id' => $album?->id, 'title' => $r['name']],
                ['audio_path' => $r['audio'], 'cover_path' => $cover, 'duration' => (int) ($r['duration'] ?? 0)]);

            if ($song->wasRecentlyCreated) $added++;
        }

        $this->line("✓ $tag: $added lagu baru (durasi penuh)");
    }
}
