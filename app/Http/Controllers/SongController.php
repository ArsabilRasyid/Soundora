<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SongController extends Controller
{
    private const AUDIO = 'file|mimes:mp3,mpga,wav,ogg,m4a,aac,flac|max:30720';

    public function index(Request $r)
    {
        $q = trim((string) $r->query('q'));
        $liked = $r->boolean('liked');

        $songs = Song::with(['artist', 'album'])
            ->when($liked, fn ($s) => $s->where('is_liked', true))
            ->when($q !== '', fn ($s) => $s->where(fn ($w) => $w
                ->where('title', 'like', "%$q%")
                ->orWhereHas('artist', fn ($a) => $a->where('name', 'like', "%$q%"))
                ->orWhereHas('album', fn ($a) => $a->where('name', 'like', "%$q%"))))
            ->latest()->get();

        return view('songs.index', [
            'songs' => $songs, 'liked' => $liked, 'q' => $q,
            'playlists' => Playlist::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('songs.create', ['artists' => Artist::orderBy('name')->pluck('name')]);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'title'     => 'required|max:120',
            'artist'    => 'required|max:80',
            'album'     => 'nullable|max:80',
            'audio'     => 'required_without:audio_url|nullable|' . self::AUDIO,
            'audio_url' => 'required_without:audio|nullable|url',
            'cover'     => 'nullable|image|max:3072',
            'duration'  => 'nullable|integer|min:0',
        ]);

        [$artist, $album] = $this->artistAlbum($d);

        Song::create([
            'title'      => trim($d['title']),
            'artist_id'  => $artist->id,
            'album_id'   => $album?->id,
            'audio_path' => $r->hasFile('audio') ? $r->file('audio')->store('songs', 'public') : $d['audio_url'],
            'cover_path' => $r->hasFile('cover') ? $r->file('cover')->store('covers', 'public') : null,
            'duration'   => $d['duration'] ?? 0,
        ]);

        return redirect()->route('songs.index')->with('ok', 'Lagu ditambahkan.');
    }

    public function edit(Song $song)
    {
        $song->load('artist', 'album');
        return view('songs.create', ['song' => $song, 'artists' => Artist::orderBy('name')->pluck('name')]);
    }

    public function update(Request $r, Song $song)
    {
        $d = $r->validate([
            'title'     => 'required|max:120',
            'artist'    => 'required|max:80',
            'album'     => 'nullable|max:80',
            'audio'     => 'nullable|' . self::AUDIO,
            'audio_url' => 'nullable|url',
            'cover'     => 'nullable|image|max:3072',
            'duration'  => 'nullable|integer|min:0',
        ]);

        [$artist, $album] = $this->artistAlbum($d);
        $data = ['title' => trim($d['title']), 'artist_id' => $artist->id, 'album_id' => $album?->id];

        if ($r->hasFile('audio') || filled($d['audio_url'] ?? null)) {
            $this->removeFile($song->audio_path);
            $data['audio_path'] = $r->hasFile('audio') ? $r->file('audio')->store('songs', 'public') : $d['audio_url'];
            $data['duration'] = $d['duration'] ?? 0;
        }
        if ($r->hasFile('cover')) {
            $this->removeFile($song->cover_path);
            $data['cover_path'] = $r->file('cover')->store('covers', 'public');
        }

        $song->update($data);

        return redirect()->route('songs.index')->with('ok', 'Lagu diperbarui.');
    }

    public function destroy(Song $song)
    {
        $this->removeFile($song->audio_path);
        $this->removeFile($song->cover_path);
        $song->delete(); // baris di playlist_song ikut terhapus (cascade)

        return back()->with('ok', 'Lagu dihapus dari SOUNDORA.');
    }

    public function like(Song $song)
    {
        $song->update(['is_liked' => ! $song->is_liked]);
        return response()->json(['liked' => $song->is_liked]);
    }

    private function artistAlbum(array $d): array
    {
        $artist = Artist::firstOrCreate(['name' => trim($d['artist'])]);
        $album = filled($d['album'] ?? null)
            ? Album::firstOrCreate(['artist_id' => $artist->id, 'name' => trim($d['album'])])
            : null;
        return [$artist, $album];
    }

    // Hanya hapus file lokal; URL eksternal dibiarkan
    private function removeFile(?string $path): void
    {
        if ($path && ! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('public')->delete($path);
        }
    }
}
