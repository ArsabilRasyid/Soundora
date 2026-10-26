@extends('layouts.app')
@section('title', $album->name)
@section('content')
@php $mins = (int) ceil($album->songs->sum('duration') / 60); @endphp
<div class="d-flex gap-4 align-items-end flex-wrap mb-4">
  <div class="hero-cover" style="background:{{ $album->cover_css }}">@unless ($album->cover_path)<i class="bi bi-disc"></i>@endunless</div>
  <div class="min-w-0">
    <small class="text-secondary fw-semibold">Album{{ $album->genre ? ' · '.$album->genre : '' }}</small>
    <h1 class="display-5 fw-bold mb-1">{{ $album->name }}</h1>
    <p class="mb-1">
      @if ($album->artist)<a class="text-decoration-none fw-semibold text-white" href="{{ route('artists.show', $album->artist) }}">{{ $album->artist->name }}</a>@endif
      <span class="text-secondary">{{ $album->year ? '· '.$album->year : '' }}</span>
    </p>
    <small class="text-secondary">{{ $album->songs->count() }} lagu{{ $mins ? ', ±'.$mins.' menit' : '' }}</small>
  </div>
</div>
<div class="d-flex flex-wrap gap-2 mb-4">
  <button class="btn btn-accent" data-playall="#list"><i class="bi bi-play-fill"></i> Putar album</button>
  <button class="btn btn-ghost" data-playall="#list" data-shuffle="1"><i class="bi bi-shuffle"></i> Acak</button>
  <div class="dropdown">
    <button class="btn btn-ghost dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-plus-lg"></i> Simpan semua ke playlist</button>
    <ul class="dropdown-menu">
      @forelse ($playlists as $pl)
        <li><form method="post" action="{{ route('albums.playlist', [$album, $pl]) }}">@csrf<button class="dropdown-item">{{ $pl->name }}</button></form></li>
      @empty
        <li><span class="dropdown-item-text text-secondary">Buat playlist dulu</span></li>
      @endforelse
    </ul>
  </div>
</div>
<div id="list" class="vstack gap-1" data-tracklist>
  @foreach ($album->songs as $s)
    @include('partials.track')
  @endforeach
</div>
@endsection
