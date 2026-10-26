@extends('layouts.app')
@section('title', $artist->name)
@section('content')
<div class="d-flex gap-4 align-items-end flex-wrap mb-4">
  <div class="hero-cover" style="border-radius:50%;background:{{ \App\Models\Song::gradient($artist->id + 300) }}"><i class="bi bi-person-fill"></i></div>
  <div>
    <small class="text-secondary fw-semibold">Artis</small>
    <h1 class="display-4 fw-bold mb-1">{{ $artist->name }}</h1>
    <small class="text-secondary">{{ $artist->songs->count() }} lagu, {{ $artist->albums->count() }} album</small>
  </div>
</div>
<div class="d-flex gap-2 mb-4">
  <button class="btn btn-accent" data-playall="#list"><i class="bi bi-play-fill"></i> Putar</button>
  <button class="btn btn-ghost" data-playall="#list" data-shuffle="1"><i class="bi bi-shuffle"></i> Acak</button>
</div>
<h2 class="h5 fw-bold mb-3">Album</h2>
<div class="row g-3 row-cols-2 row-cols-md-4 row-cols-xl-6 mb-5">
  @foreach ($artist->albums as $al)
    <div class="col"><a href="{{ route('albums.show', $al) }}" class="pl-card">
      <div class="pl-cover" style="background:{{ $al->cover_css }}">@unless ($al->cover_path)<i class="bi bi-disc"></i>@endunless</div>
      <div class="fw-bold text-truncate">{{ $al->name }}</div>
      <small class="text-secondary">{{ $al->year }}</small>
    </a></div>
  @endforeach
</div>
<h2 class="h5 fw-bold mb-3">Semua lagu</h2>
<div id="list" class="vstack gap-1" data-tracklist>
  @foreach ($artist->songs as $s)
    @include('partials.track')
  @endforeach
</div>
@endsection
