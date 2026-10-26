<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Lagu contoh dari SoundHelix (butuh internet). Ganti dengan upload lagu sendiri kapan saja.
        $demo = [
            ['Night Running', 'Aurora Wave', 'Midnight Drive'],
            ['Easy Does It', 'Lofi Kopi', 'Warung 24 Jam'],
            ['Strangers', 'Biosfer', 'Kota Tanpa Nama'],
            ['Spit It Out', 'Solar Kid', 'Spit It Out'],
            ['Pagi di Kalimantan', 'Lofi Kopi', 'Warung 24 Jam'],
            ['Head in the Clouds', 'Aurora Wave', 'Midnight Drive'],
        ];

        foreach ($demo as $n => [$title, $artistName, $albumName]) {
            $artist = Artist::firstOrCreate(['name' => $artistName]);
            $album = Album::firstOrCreate(['artist_id' => $artist->id, 'name' => $albumName]);
            Song::create([
                'title' => $title, 'artist_id' => $artist->id, 'album_id' => $album->id,
                'audio_path' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-' . ($n + 1) . '.mp3',
            ]);
        }

        $chill = Playlist::create(['name' => 'Chill Pills', 'description' => 'Lagu santai untuk menemani belajar.']);
        $chill->songs()->attach([2 => ['position' => 0], 5 => ['position' => 1], 3 => ['position' => 2]]);
    }
}
