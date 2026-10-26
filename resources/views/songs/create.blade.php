@extends('layouts.app')
@section('title', isset($song) ? 'Edit Lagu' : 'Tambah Lagu')
@section('content')
@php $edit = isset($song); @endphp
<h1 class="h2 fw-bold mb-4">{{ $edit ? 'Edit lagu' : 'Tambah lagu' }}</h1>
@if ($edit && $song->isPreview())
  <div class="alert alert-info" style="max-width:640px">Lagu ini hanya cuplikan 30 detik dari iTunes. Unggah file lengkap milikmu di bawah untuk menggantinya.</div>
@endif
<form method="post" action="{{ $edit ? route('songs.update', $song) : route('songs.store') }}" enctype="multipart/form-data" class="vstack gap-3" style="max-width:640px">
  @csrf @if ($edit) @method('PUT') @endif
  <input type="hidden" name="duration" id="duration" value="{{ old('duration', $song->duration ?? 0) }}">
  <div><label class="form-label">Judul</label><input name="title" class="form-control" required maxlength="120" value="{{ old('title', $song->title ?? '') }}"></div>
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">Artis</label><input name="artist" list="artists" class="form-control" required maxlength="80" value="{{ old('artist', $song->artist->name ?? '') }}"><datalist id="artists">@foreach ($artists as $a)<option value="{{ $a }}">@endforeach</datalist></div>
    <div class="col-md-6"><label class="form-label">Album <span class="text-secondary">(opsional)</span></label><input name="album" class="form-control" maxlength="80" value="{{ old('album', $song->album->name ?? '') }}"></div>
  </div>
  <div><label class="form-label">{{ $edit ? 'Ganti file audio' : 'File audio' }} <span class="text-secondary">({{ $edit ? 'kosongkan jika tidak diganti · ' : '' }}mp3, wav, ogg, m4a, flac · maks 30 MB)</span></label><input type="file" id="audio" name="audio" accept="audio/*" class="form-control"></div>
  <div><label class="form-label">…atau tautan audio</label><input type="url" id="audio_url" name="audio_url" class="form-control" placeholder="https://contoh.com/lagu.mp3" value="{{ old('audio_url') }}"></div>
  <div><label class="form-label">Sampul <span class="text-secondary">(opsional)</span></label><input type="file" name="cover" accept="image/*" class="form-control"></div>
  <div class="d-flex gap-2"><button class="btn btn-accent">{{ $edit ? 'Simpan perubahan' : 'Simpan lagu' }}</button><a href="{{ route('songs.index') }}" class="btn btn-ghost">Batal</a></div>
</form>
<script>
(() => {
  const f = document.getElementById('audio'), u = document.getElementById('audio_url'), d = document.getElementById('duration');
  const probe = src => { const a = new Audio(); a.preload = 'metadata'; a.onloadedmetadata = () => { d.value = Math.round(a.duration) || 0; }; a.src = src; };
  f.onchange = () => f.files[0] && probe(URL.createObjectURL(f.files[0]));
  u.onchange = () => u.value && probe(u.value);
})();
</script>
@endsection
