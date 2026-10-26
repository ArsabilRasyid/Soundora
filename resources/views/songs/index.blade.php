@extends('layouts.app')
@section('title', $liked ? 'Lagu Disukai' : 'Library')
@section('content')
<div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-3">
  <h1 class="h2 fw-bold m-0">{{ $liked ? 'Lagu Disukai' : 'Library' }}</h1>
  <form method="get" class="d-flex gap-2">
    @if ($liked)<input type="hidden" name="liked" value="1">@endif
    <input name="q" value="{{ $q }}" class="form-control" placeholder="Cari judul atau artis">
    <button class="btn btn-ghost"><i class="bi bi-search"></i></button>
  </form>
</div>
<div class="d-flex gap-2 mb-4">
  <button class="btn btn-accent" data-playall="#list"><i class="bi bi-play-fill"></i> Putar semua</button>
  <button class="btn btn-ghost" data-playall="#list" data-shuffle="1"><i class="bi bi-shuffle"></i> Acak</button>
  <a href="{{ route('songs.create') }}" class="btn btn-ghost ms-auto"><i class="bi bi-plus-lg"></i> Tambah lagu</a>
</div>
<div id="list" class="vstack gap-1" data-tracklist>
  @forelse ($songs as $s)
    @include('partials.track')
  @empty
    <div class="text-center text-secondary py-5">{{ $liked ? 'Belum ada lagu yang disukai. Tekan ikon hati pada lagu.' : 'Tidak ada lagu. Tambahkan lagu pertamamu.' }}</div>
  @endforelse
</div>
@endsection
