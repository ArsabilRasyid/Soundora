@extends('layouts.app')
@section('title', 'Playlist')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h2 fw-bold m-0">Playlist</h1>
  <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#newPl"><i class="bi bi-plus-lg"></i> Playlist baru</button>
</div>
<div class="row g-3 row-cols-2 row-cols-md-3 row-cols-xl-4">
  @forelse ($lists as $pl)
    <div class="col">
      <a href="{{ route('playlists.show', $pl) }}" class="pl-card">
        <div class="pl-cover" style="background:{{ \App\Models\Song::gradient($pl->id) }}"><i class="bi bi-music-note-beamed"></i></div>
        <div class="fw-bold text-truncate">{{ $pl->name }}</div>
        <small class="text-secondary d-block text-truncate">{{ $pl->description ?: 'Tanpa deskripsi' }}</small>
        <small class="text-secondary">{{ $pl->songs_count }} lagu</small>
      </a>
    </div>
  @empty
    <div class="col-12 text-center text-secondary py-5">Belum ada playlist. Buat yang pertama, misalnya “Belajar Coding”.</div>
  @endforelse
</div>
@include('partials.playlist-modal')
@endsection
