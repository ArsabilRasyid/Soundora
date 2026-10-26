<?php

namespace App\Console\Commands;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Impor file audio dari folder musikmu (mp3, m4a, wav, ogg, flac) beserta album dan foto/sampul.
 *
 * Struktur yang dikenali:
 *   D:/Musik/Artis/Album/01 - Judul.mp3     (+ cover.jpg di folder album, + "01 - Judul.jpg" untuk foto per lagu)
 *   D:/Musik/Album/01 - Judul.mp3
 *   D:/Musik/Artis - Judul.mp3
 * Tag ID3 (judul, artis, album, tahun, sampul tertanam) dipakai lebih dulu; nama folder/file jadi cadangan.
 */
class ScanMusic extends Command
{
    protected $signature = 'soundora:scan
        {path=D:/Musik : Folder berisi file audio}
        {--clean : Hapus dulu lagu cuplikan 30 detik (iTunes), lagu contoh SoundHelix, dan lagu Jamendo}';

    protected $description = 'Impor lagu, album, dan foto dari folder musik lokal';

    public function handle(): int
    {
        if (! class_exists(\getID3::class)) {
            $this->error('Jalankan dulu: composer require james-heinrich/getid3');
            return self::FAILURE;
        }
        $path = rtrim($this->argument('path'), '/\\');
        if (! is_dir($path)) { $this->error("Folder tidak ditemukan: $path"); return self::FAILURE; }

        if ($this->option('clean')) {
            Song::where(fn ($q) => $q->where('audio_path', 'like', '%itunes.apple.com%')
                ->orWhere('audio_path', 'like', '%mzstatic.com%')
                ->orWhere('audio_path', 'like', '%soundhelix%')
                ->orWhere('audio_path', 'like', '%jamendo.com%'))->delete();
            Album::doesntHave('songs')->delete();
            Artist::doesntHave('songs')->delete();
            $this->info('Lagu cuplikan/contoh dihapus.');
        }

        $getID3 = new \getID3;
        $new = $upgraded = $skipped = 0;
        $cache = [];

        // simpan gambar sekali saja (di-cache berdasarkan isinya)
        $saveImg = function (string $data, string $ext) use (&$cache) {
            return $cache[md5($data)] ??= tap('covers/' . Str::random(14) . '.' . strtolower($ext),
                fn ($p) => Storage::disk('public')->put($p, $data));
        };

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $f) {
            $ext = strtolower($f->getExtension());
            if (! in_array($ext, ['mp3', 'm4a', 'wav', 'ogg', 'flac'])) continue;

            $info = $getID3->analyze($f->getPathname());
            \getid3_lib::CopyTagsToComments($info);
            $c = $info['comments'] ?? [];
            $dur = (int) round($info['playtime_seconds'] ?? 0);

            // ---- cadangan dari struktur folder & nama file ----
            $parts = explode('/', trim(str_replace('\\', '/', substr($f->getPathname(), strlen($path))), '/'));
            $base = pathinfo($f->getFilename(), PATHINFO_FILENAME);
            $name = preg_replace('/^\d+\s*[-.]\s*/', '', $base);            // buang nomor urut "01 - "
            $fileArtist = null; $fileTitle = $name;
            if (str_contains($name, ' - ')) [$fileArtist, $fileTitle] = array_map('trim', explode(' - ', $name, 2));
            $folderArtist = count($parts) >= 3 ? $parts[0] : null;
            $folderAlbum = count($parts) >= 3 ? $parts[1] : (count($parts) === 2 ? $parts[0] : null);

            $title = trim($c['title'][0] ?? $fileTitle);
            $artistName = trim($c['artist'][0] ?? $folderArtist ?? $fileArtist ?? 'Artis Tidak Diketahui');
            $albumName = trim($c['album'][0] ?? $folderAlbum ?? '');

            // ---- foto album & foto lagu ----
            $dir = $f->getPath();
            $findImg = function (array $names) use ($dir) {
                foreach ($names as $n) foreach (['jpg', 'jpeg', 'png', 'webp'] as $x) {
                    if (is_file("$dir/$n.$x")) return "$dir/$n.$x";
                }
                return null;
            };

            $albumCover = null;
            if (! empty($c['picture'][0]['data'])) {
                $albumCover = $saveImg($c['picture'][0]['data'], str_contains($c['picture'][0]['image_mime'] ?? '', 'png') ? 'png' : 'jpg');
            } elseif ($img = $findImg(['cover', 'folder', 'front', 'album', 'albumart'])) {
                $albumCover = $saveImg(file_get_contents($img), pathinfo($img, PATHINFO_EXTENSION));
            }
            // foto khusus lagu: gambar dengan nama sama dengan file lagu ("01 - Judul.jpg")
            $songCover = ($img = $findImg([$base])) ? $saveImg(file_get_contents($img), pathinfo($img, PATHINFO_EXTENSION)) : $albumCover;

            // ---- simpan ke database ----
            $artist = Artist::firstOrCreate(['name' => $artistName]);
            $existing = Song::where('artist_id', $artist->id)->whereRaw('lower(title) = ?', [mb_strtolower($title)])->first();
            if ($existing && ! $existing->isRemote()) { $skipped++; continue; } // sudah punya file lengkap

            $stored = Storage::disk('public')->putFileAs('songs', new File($f->getPathname()), Str::random(14) . '.' . $ext);

            $album = null;
            if ($albumName !== '') {
                $album = Album::firstOrCreate(
                    ['artist_id' => $artist->id, 'name' => $albumName],
                    ['cover_path' => $albumCover, 'year' => (int) ($c['year'][0] ?? 0) ?: null, 'genre' => $c['genre'][0] ?? null]);
                if (! $album->cover_path && $albumCover) $album->update(['cover_path' => $albumCover]);
            }

            if ($existing) { // ganti cuplikan dengan file lengkap
                $existing->update(['audio_path' => $stored, 'duration' => $dur,
                    'album_id' => $existing->album_id ?? $album?->id, 'cover_path' => $songCover ?? $existing->cover_path]);
                $upgraded++;
            } else {
                Song::create(['title' => $title, 'artist_id' => $artist->id, 'album_id' => $album?->id,
                              'audio_path' => $stored, 'cover_path' => $songCover, 'duration' => $dur]);
                $new++;
            }
            $this->line("✓ $artistName – $title" . ($albumName ? " [$albumName]" : ''));
        }

        $this->info("Selesai: $new baru, $upgraded cuplikan diganti file lengkap, $skipped sudah ada.");
        return self::SUCCESS;
    }
}
