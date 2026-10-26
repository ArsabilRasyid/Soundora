@extends('layouts.app')
@section('title', 'Album')
@section('content')
<div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4">
  <h1 class="h2 fw-bold m-0">Album</h1>
  <form method="get" class="d-flex gap-2">
    <input name="q" value="{{ $q }}" class="form-control" placeholder="Cari album atau artis">
    <button class="btn btn-ghost" aria-label="Cari"><i class="bi bi-search"></i></button>
  </form>
</div>
<div class="row g-3 row-cols-2 row-cols-md-3 row-cols-xl-5">
  @forelse ($albums as $al)
    <div class="col">
      <a href="{{ route('albums.show', $al) }}" class="pl-card">
        <div class="pl-cover" style="background:{{ $al->cover_css }}">@unless ($al->cover_path)<i class="bi bi-disc"></i>@endunless</div>
        <div class="fw-bold text-truncate">{{ $al->name }}</div>
        <small class="text-secondary d-block text-truncate">{{ $al->artist->name ?? 'Artis tidak diketahui' }}{{ $al->year ? ', '.$al->year : '' }}</small>
        <small class="text-secondary">{{ $al->songs_count }} lagu</small>
      </a>
    </div>
  @empty
    <div class="col-12 text-center text-secondary py-5">Belum ada album. Jalankan <code>php artisan soundora:import</code> atau tambahkan lagu dengan nama album.</div>
  @endforelse
</div>
@endsection
