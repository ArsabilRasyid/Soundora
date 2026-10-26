<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Playlist;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string) $r->query('q'));

        $albums = Album::with('artist')->withCount('songs')->has('songs')
            ->when($q !== '', fn ($a) => $a->where(fn ($w) => $w
                ->where('name', 'like', "%$q%")
                ->orWhereHas('artist', fn ($x) => $x->where('name', 'like', "%$q%"))))
            ->orderBy('name')->get();

        return view('albums.index', compact('albums', 'q'));
    }

    public function show(Album $album)
    {
        $album->load('artist', 'songs.artist', 'songs.album');

        return view('albums.show', ['album' => $album, 'playlists' => Playlist::orderBy('name')->get()]);
    }

    // Simpan seluruh lagu album ke sebuah playlist (lagu yang sudah ada dilewati)
    public function addToPlaylist(Album $album, Playlist $playlist)
    {
        $have = $playlist->songs()->pluck('songs.id');
        $pos = $have->count();
        $new = 0;

        foreach ($album->songs as $song) {
            if ($have->contains($song->id)) continue;
            $playlist->songs()->attach($song->id, ['position' => $pos++]);
            $new++;
        }

        return back()->with('ok', "$new lagu dari album “{$album->name}” ditambahkan ke {$playlist->name}.");
    }
}
