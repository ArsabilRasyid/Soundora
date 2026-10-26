@extends('layouts.app')
@section('title', $playlist->name)
@section('content')
@php $mins = (int) ceil($playlist->songs->sum('duration') / 60); @endphp
<div class="d-flex gap-4 align-items-end flex-wrap mb-4">
  <div class="hero-cover" style="background:{{ \App\Models\Song::gradient($playlist->id) }}"><i class="bi bi-music-note-beamed"></i></div>
  <div class="min-w-0">
    <small class="text-secondary fw-semibold">Playlist</small>
    <h1 class="display-5 fw-bold mb-1">{{ $playlist->name }}</h1>
    <p class="text-secondary mb-1">{{ $playlist->description ?: 'Tambahkan deskripsi lewat tombol Edit.' }}</p>
    <small class="text-secondary">{{ $playlist->songs->count() }} lagu{{ $mins ? ', ±'.$mins.' menit' : '' }}</small>
  </div>
</div>
<div class="d-flex flex-wrap gap-2 mb-4">
  <button class="btn btn-accent" data-playall="#list"><i class="bi bi-play-fill"></i> Putar</button>
  <button class="btn btn-ghost" data-playall="#list" data-shuffle="1"><i class="bi bi-shuffle"></i> Acak</button>
  <button class="btn btn-ghost" data-bs-toggle="modal" data-bs-target="#addSong"><i class="bi bi-plus-lg"></i> Tambah lagu</button>
  <button class="btn btn-ghost" data-bs-toggle="modal" data-bs-target="#editPl"><i class="bi bi-pencil"></i> Edit</button>
  <form method="post" action="{{ route('playlists.destroy', $playlist) }}" data-turbo-confirm="Hapus playlist “{{ $playlist->name }}”? Lagu aslinya tidak ikut terhapus.">
    @csrf @method('DELETE')<button class="btn btn-ghost text-danger"><i class="bi bi-trash"></i> Hapus</button>
  </form>
</div>

<div id="list" class="vstack gap-1" data-tracklist data-sortable data-url="{{ route('playlists.reorder', $playlist) }}">
  @forelse ($playlist->songs as $s)
    @include('partials.track')
  @empty
    <div class="text-center text-secondary py-5" data-empty>Playlist ini masih kosong. Tekan “Tambah lagu” untuk mengisinya.</div>
  @endforelse
</div>

<div class="modal fade" id="addSong" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-0"><h5 class="modal-title">Tambah lagu ke {{ $playlist->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
      <div class="px-3 pb-2"><input class="form-control" placeholder="Cari lagu…" oninput="document.querySelectorAll('#avail li').forEach(li=>li.hidden=!li.textContent.toLowerCase().includes(this.value.toLowerCase()))"></div>
      <ul id="avail" class="list-unstyled modal-body vstack gap-1 pt-0">
        @forelse ($available as $s)
          <li><form method="post" action="{{ route('playlists.songs.add', [$playlist, $s]) }}" class="d-flex align-items-center gap-3 p-2 rounded">
            @csrf
            <div class="cov" style="background:{{ $s->cover_css }}"></div>
            <div class="flex-grow-1 min-w-0"><div class="fw-bold text-truncate">{{ $s->title }}</div><small class="text-secondary">{{ $s->artist->name }}</small></div>
            <button class="btn btn-ghost btn-sm" aria-label="Tambahkan {{ $s->title }}"><i class="bi bi-plus-lg"></i></button>
          </form></li>
        @empty
          <li class="text-secondary text-center py-3">Semua lagu sudah ada di playlist ini.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
@include('partials.playlist-modal', ['p' => $playlist])
@endsection
