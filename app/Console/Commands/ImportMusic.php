<?php

namespace App\Console\Commands;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Mengisi SOUNDORA dengan lagu populer dari iTunes Search API (gratis, tanpa API key).
 * Yang diambil: judul, artis, album, tahun, sampul, dan cuplikan audio 30 detik resmi dari Apple.
 */
class ImportMusic extends Command
{
    protected $signature = 'soundora:import
        {artists?* : Nama artis, mis. "Daniel Caesar" "Billie Eilish"}
        {--theme=* : Kata kunci bebas, mis. --theme="funk 2026"}
        {--limit=40 : Maks. lagu per pencarian}
        {--country=US : Kode negara toko iTunes (US, ID, BR, ...)}
        {--fresh : Hapus lagu contoh SoundHelix lebih dulu}';

    protected $description = 'Impor lagu, album, dan sampul populer dari iTunes Search API';

    private const ARTISTS = [
        'Daniel Caesar',
        'Billie Eilish',
        'The Weeknd',
        'Dua Lipa',
        'Bruno Mars',
        'Taylor Swift',
        'Olivia Rodrigo',
        'Sabrina Carpenter',
        'SZA',
        'Kendrick Lamar',
        'Tame Impala',
        'Arctic Monkeys',
        'Ed Sheeran',
        'Coldplay',
        'Tulus',
        'Raisa',
        'Hindia',
        'Nadin Amizah',
        'Juicy Luicy',
        'Mahalini',
    ];

    private const THEMES = [''];

    public function handle(): int
    {
        if ($this->option('fresh')) {
            Song::where('audio_path', 'like', '%soundhelix%')->delete();
            Album::doesntHave('songs')->delete();
            Artist::doesntHave('songs')->delete();
            $this->info('Lagu contoh SoundHelix dihapus.');
        }

        $artists = $this->argument('artists');
        $themes = $this->option('theme');
        if (! $artists && ! $themes) {
            $artists = self::ARTISTS;
            $themes = self::THEMES;
        }

        foreach ($artists as $a) $this->import($a, true);
        foreach ($themes as $t) $this->import($t, false);

        $this->info('Selesai. Total: ' . Song::count() . ' lagu, ' . Album::count() . ' album, ' . Artist::count() . ' artis.');
        return self::SUCCESS;
    }

    private function import(string $term, bool $byArtist): void
    {
        $params = [
            'term' => $term,
            'media' => 'music',
            'entity' => 'song',
            'limit' => (int) $this->option('limit'),
            'country' => $this->option('country')
        ];
        if ($byArtist) $params['attribute'] = 'artistTerm';

        try {
            $results = Http::timeout(25)->retry(3, 5000)->get('https://itunes.apple.com/search', $params)->json('results') ?? [];
        } catch (\Throwable $e) {
            $this->error("Gagal mengambil “$term”: " . $e->getMessage());
            return;
        }

        $added = 0;
        foreach ($results as $r) {
            if (empty($r['previewUrl']) || empty($r['trackName']) || empty($r['artistName'])) continue;
            if ($byArtist && ! str_contains(mb_strtolower($r['artistName']), mb_strtolower($term))) continue;

            $cover = isset($r['artworkUrl100']) ? str_replace('100x100bb', '600x600bb', $r['artworkUrl100']) : null;
            $artist = Artist::firstOrCreate(['name' => $r['artistName']]);
            $album = ! empty($r['collectionName'])
                ? Album::firstOrCreate(
                    ['artist_id' => $artist->id, 'name' => $r['collectionName']],
                    [
                        'cover_path' => $cover,
                        'genre' => $r['primaryGenreName'] ?? null,
                        'year' => isset($r['releaseDate']) ? (int) substr($r['releaseDate'], 0, 4) : null
                    ]
                )
                : null;

            $song = Song::firstOrCreate(
                ['artist_id' => $artist->id, 'album_id' => $album?->id, 'title' => $r['trackName']],
                [
                    'audio_path' => $r['previewUrl'],
                    'cover_path' => $cover,
                    'duration' => 30
                ]
            ); // cuplikan resmi 30 detik

            if ($song->wasRecentlyCreated) $added++;
        }

        $this->line("✓ $term: $added lagu baru");
        sleep(3); // iTunes membatasi ±20 permintaan/menit
    }
}
