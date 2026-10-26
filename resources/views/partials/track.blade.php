<div class="track" data-id="{{ $s->id }}" data-title="{{ $s->title }}" data-artist="{{ $s->artist->name }}" data-src="{{ $s->audio_url }}" data-cover="{{ $s->cover_css }}">
  @isset($playlist)<i class="bi bi-grip-vertical drag" title="Seret untuk mengubah urutan"></i>@endisset
  <button type="button" class="ibtn" data-play title="Putar"><i class="bi bi-play-fill"></i></button>
  <div class="cov" style="background:{{ $s->cover_css }}"></div>
  <div class="t-main"><div class="t-title text-truncate">{{ $s->title }}@if ($s->isPreview())<span class="badge rounded-pill ms-2" style="background:var(--panel2);color:var(--mut)" title="Hanya cuplikan 30 detik. Menu ⋯ → Edit untuk mengganti dengan file lengkap.">30 dtk</span>@endif</div><a class="small text-secondary text-decoration-none" href="{{ route('artists.show', $s->artist) }}">{{ $s->artist->name }}</a></div>
  <div class="t-album d-none d-md-block text-secondary text-truncate">@if ($s->album)<a class="text-secondary text-decoration-none" href="{{ route('albums.show', $s->album) }}">{{ $s->album->name }}</a>@else — @endif</div>
  <button type="button" class="ibtn" data-like data-url="{{ route('songs.like', $s) }}" title="Suka"><i class="bi {{ $s->is_liked ? 'bi-heart-fill text-acc' : 'bi-heart' }}"></i></button>
  <small class="text-secondary t-dur">{{ $s->duration_label }}</small>
  <div class="dropdown">
    <button type="button" class="ibtn" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-label="Menu lagu"><i class="bi bi-three-dots"></i></button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="{{ route('songs.edit', $s) }}"><i class="bi bi-pencil me-2"></i>Edit / ganti audio</a></li>
      @isset($playlist)
        <li><form method="post" action="{{ route('playlists.songs.remove', [$playlist, $s]) }}" data-turbo-confirm="Keluarkan “{{ $s->title }}” dari playlist ini? Lagunya tetap ada di library.">@csrf @method('DELETE')
          <button class="dropdown-item"><i class="bi bi-dash-circle me-2"></i>Keluarkan dari playlist</button></form></li>
      @else
        <li><h6 class="dropdown-header">Tambah ke playlist</h6></li>
        @forelse ($playlists as $pl)
          <li><form method="post" action="{{ route('playlists.songs.add', [$pl, $s]) }}">@csrf<button class="dropdown-item">{{ $pl->name }}</button></form></li>
        @empty
          <li><span class="dropdown-item-text text-secondary">Belum ada playlist</span></li>
        @endforelse
        <li><hr class="dropdown-divider"></li>
        <li><form method="post" action="{{ route('songs.destroy', $s) }}" data-turbo-confirm="Hapus “{{ $s->title }}” dari SOUNDORA? Lagu juga hilang dari semua playlist.">@csrf @method('DELETE')
          <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Hapus lagu</button></form></li>
      @endisset
    </ul>
  </div>
</div>
