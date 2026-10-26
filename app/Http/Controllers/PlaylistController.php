<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    private function rules(): array
    {
        return ['name' => 'required|string|max:60', 'description' => 'nullable|string|max:255'];
    }

    public function index()
    {
        return view('playlists.index', ['lists' => Playlist::withCount('songs')->latest()->get()]);
    }

    public function store(Request $r)
    {
        $p = Playlist::create($r->validate($this->rules()));
        return redirect()->route('playlists.show', $p)->with('ok', 'Playlist dibuat. Yuk tambahkan lagu!');
    }

    public function show(Playlist $playlist)
    {
        $playlist->load('songs.artist', 'songs.album');

        return view('playlists.show', [
            'playlist'  => $playlist,
            'available' => Song::with('artist')->whereNotIn('id', $playlist->songs->pluck('id'))->orderBy('title')->get(),
        ]);
    }

    public function update(Request $r, Playlist $playlist)
    {
        $playlist->update($r->validate($this->rules()));
        return back()->with('ok', 'Playlist diperbarui.');
    }

    public function destroy(Playlist $playlist)
    {
        $playlist->delete(); // hanya playlist & relasinya; lagu asli tetap aman
        return redirect()->route('playlists.index')->with('ok', 'Playlist dihapus. Lagunya tetap ada di library.');
    }

    public function addSong(Playlist $playlist, Song $song)
    {
        $playlist->songs()->syncWithoutDetaching([$song->id => ['position' => $playlist->songs()->count()]]);
        return back()->with('ok', "“{$song->title}” ditambahkan ke {$playlist->name}.");
    }

    public function removeSong(Playlist $playlist, Song $song)
    {
        $playlist->songs()->detach($song->id); // hanya lepas relasi, lagu asli tidak dihapus
        return back()->with('ok', "“{$song->title}” dikeluarkan dari playlist.");
    }

    public function reorder(Request $r, Playlist $playlist)
    {
        $order = $r->validate(['order' => 'required|array', 'order.*' => 'integer'])['order'];
        foreach ($order as $pos => $songId) {
            $playlist->songs()->updateExistingPivot($songId, ['position' => $pos]);
        }
        return response()->json(['ok' => true]);
    }
}
