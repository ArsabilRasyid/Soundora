<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Playlist;

class ArtistController extends Controller
{
    public function show(Artist $artist)
    {
        $artist->load(['albums' => fn ($a) => $a->withCount('songs')->has('songs')->orderByDesc('year'), 'songs.artist', 'songs.album']);

        return view('artists.show', ['artist' => $artist, 'playlists' => Playlist::orderBy('name')->get()]);
    }
}
