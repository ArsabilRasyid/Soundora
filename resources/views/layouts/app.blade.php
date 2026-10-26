<!doctype html>
<html lang="id" data-bs-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Library') · SOUNDORA</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.4/howler.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
{{-- Turbo: pindah halaman tanpa reload penuh, sehingga musik tidak putus --}}
<script type="module">import 'https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.12/+esm';</script>
<style>
:root{--bg:#0f1120;--panel:#171a2e;--panel2:#20243d;--line:#2b3052;--acc:#ff5c7c;--teal:#2dd4bf;--mut:#8f95b5}
body{background:var(--bg);font-family:'DM Sans',system-ui,sans-serif;height:100vh;overflow:hidden}
h1,h2,h3,.brand{font-family:'Sora',sans-serif;letter-spacing:-.02em}
.shell{display:grid;grid-template-columns:250px 1fr;grid-template-rows:minmax(0,1fr) 92px;height:100vh;gap:8px;padding:8px}
.side,.main{background:var(--panel);border-radius:14px;overflow-y:auto}
.main{padding:28px 32px;scroll-behavior:smooth}
.side{padding:20px 14px}
.brand{font-size:1.35rem;font-weight:800;color:#fff;text-decoration:none;display:flex;align-items:center;gap:.5rem;padding:0 8px 18px}
.brand i{color:var(--acc)}
.nav-l{display:flex;align-items:center;gap:.75rem;padding:.55rem .75rem;border-radius:10px;color:var(--mut);text-decoration:none;font-weight:500}
.nav-l:hover,.nav-l.active{color:#fff;background:var(--panel2)}
.mini{width:22px;height:22px;border-radius:6px;flex:none}
.text-acc{color:var(--acc)!important}
.btn-accent{background:var(--acc);border:0;color:#fff;font-weight:700;border-radius:999px;padding:.5rem 1.25rem}
.btn-accent:hover{background:#ff7690;color:#fff}
.btn-ghost{border:1px solid var(--line);color:#fff;border-radius:999px;padding:.5rem 1.1rem}
.btn-ghost:hover{background:var(--panel2);color:#fff}
.form-control,.form-select{background:var(--panel2);border-color:var(--line)}
.form-control:focus{background:var(--panel2);border-color:var(--teal);box-shadow:0 0 0 .2rem rgba(45,212,191,.15)}
/* daftar lagu */
.track{display:flex;align-items:center;gap:12px;padding:8px 10px;border-radius:10px;background:var(--panel)}
.track:hover{background:var(--panel2)}
.track.playing .t-title{color:var(--teal)}
.t-main{flex:2;min-width:0}.t-album{flex:1;min-width:0}.t-title{font-weight:700}
.t-dur{width:40px;text-align:right}
.cov{width:44px;height:44px;border-radius:8px;flex:none;background-size:cover}
.ibtn{background:none;border:0;color:var(--mut);font-size:1.15rem;width:34px;height:34px;border-radius:50%;flex:none}
.ibtn:hover{color:#fff}
.drag{color:var(--mut);cursor:grab;font-size:1.2rem}
.sortable-ghost{opacity:.35}
/* kartu playlist & hero */
.pl-card{display:block;text-decoration:none;color:#fff;background:var(--panel2);border-radius:14px;padding:14px;height:100%;transition:transform .15s}
.pl-card:hover{transform:translateY(-3px);color:#fff}
.pl-cover{aspect-ratio:1;border-radius:10px;display:grid;place-items:center;font-size:2.6rem;color:rgba(255,255,255,.85);margin-bottom:12px}
.hero-cover{width:200px;height:200px;border-radius:14px;display:grid;place-items:center;font-size:4.5rem;color:rgba(255,255,255,.85);box-shadow:0 18px 40px rgba(0,0,0,.45)}
/* player */
#player{grid-column:1/-1;background:var(--panel);border-radius:14px;display:grid;grid-template-columns:1fr minmax(280px,2fr) 1fr;align-items:center;gap:16px;padding:0 20px}
#pCover{width:56px;height:56px;border-radius:8px;background:var(--panel2);flex:none}
.pbtn{background:none;border:0;color:var(--mut);font-size:1.25rem}
.pbtn:hover,.pbtn.on{color:var(--teal)}
#pPlay{font-size:2.5rem;color:#fff}
.seek{width:100%;accent-color:var(--acc);height:4px}
@media(max-width:767px){
 body{overflow:auto;height:auto}
 .shell{display:block;height:auto;padding:8px 8px 150px}
 .side{margin-bottom:8px;padding:10px}.side .pl-list{display:none}
 .main{padding:18px 14px;overflow:visible}
 #player{position:fixed;left:8px;right:8px;bottom:8px;grid-template-columns:1fr;height:132px;padding:8px 14px;gap:2px;z-index:50}
 #player .vol{display:none}.hero-cover{width:130px;height:130px;font-size:3rem}
}
</style>
<script>
window.SD = (() => {
  let q = [], i = -1, token = 0, errs = 0, hist = [], h = null, shuf = false, rep = 0, vol = parseFloat(localStorage.getItem('sdVol') || '0.8');
  const $ = s => document.querySelector(s);
  const fmt = s => { s = Math.floor(s || 0); return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0'); };
  const cur = () => q[i];
  const csrf = () => document.querySelector('meta[name=csrf-token]').content;

  function mark() {
    document.querySelectorAll('.track').forEach(r => {
      const on = cur() && r.dataset.id === cur().id;
      r.classList.toggle('playing', !!on);
      const ic = r.querySelector('[data-play] i');
      if (ic) ic.className = 'bi ' + (on && h && h.playing() ? 'bi-pause-fill' : 'bi-play-fill');
    });
  }
  function ui() {
    const t = cur(); if (!t) return;
    $('#pTitle').textContent = t.title; $('#pArtist').textContent = t.artist; $('#pCover').style.background = t.cover;
    $('#pPlay').className = 'bi ' + (h && h.playing() ? 'bi-pause-circle-fill' : 'bi-play-circle-fill');
    if ('mediaSession' in navigator) navigator.mediaSession.metadata = new MediaMetadata({ title: t.title, artist: t.artist });
    mark();
  }
  function toast(m) {
    const d = document.createElement('div');
    d.className = 'alert alert-warning py-2 position-fixed';
    d.style.cssText = 'right:16px;top:16px;z-index:2000;max-width:320px';
    d.textContent = m; document.body.appendChild(d); setTimeout(() => d.remove(), 3500);
  }
  // Howler menebak codec dari ekstensi URL; tentukan sendiri supaya URL tanpa ekstensi tidak gagal
  const extOf = u => { const m = /\.([a-z0-9]{2,5})$/i.exec(u.split('?')[0]); return m ? m[1].toLowerCase() : 'mp3'; };

  function load(n, fromHist) {
    if (!fromHist && i >= 0 && n !== i) { hist.push(i); if (hist.length > 50) hist.shift(); }
    i = n; const t = cur(), my = ++token;
    // lepas semua event lagu lama agar onend/onstop lama tidak memicu lompat ganda
    if (h) { h.off(); h.stop(); h.unload(); }
    h = new Howl({
      src: [t.src], format: [extOf(t.src)], html5: true, volume: vol,
      onload: () => document.querySelectorAll('.track[data-id="' + t.id + '"] .t-dur').forEach(x => { if (x.textContent.trim() === '—') x.textContent = fmt(h.duration()); }),
      onplay: () => { if (my === token) { errs = 0; ui(); } },
      onpause: () => { if (my === token) ui(); },
      onstop: () => { if (my === token) ui(); },
      onend: () => { if (my !== token) return; rep === 2 ? (h.seek(0), h.play()) : next(); },
      // browser memblokir autoplay saat lagu berganti sendiri: coba lagi begitu audio terbuka
      onplayerror: () => { if (my === token) h.once('unlock', () => h.play()); },
      // lagu gagal dimuat (link mati / format tidak didukung): beri tahu lalu lompat ke berikutnya
      onloaderror: () => { if (my !== token) return; toast('Gagal memuat: ' + t.title); skip(); }
    });
    h.play(); ui();
  }
  function skip() {
    if (++errs >= q.length) { errs = 0; h && h.stop(); ui(); return; } // semua lagu gagal: berhenti, jangan berputar tanpa akhir
    setTimeout(next, 500);
  }
  function next() {
    if (!q.length) return;
    let n;
    if (shuf && q.length > 1) { do { n = Math.floor(Math.random() * q.length); } while (n === i); }
    else n = i + 1;
    if (n >= q.length) { if (rep === 1) n = 0; else { h && h.stop(); ui(); return; } }
    load(n);
  }
  function prev() {
    if (!q.length) return;
    if (h && h.seek() > 3) h.seek(0);
    else if (hist.length) load(hist.pop(), true);
    else load(Math.max(i - 1, 0), true);
  }
  function toggle() { if (h) h.playing() ? h.pause() : h.play(); }
  function build(list) {
    i = -1; hist = []; errs = 0;
    q = [...list.querySelectorAll('.track')].map(r => ({ id: r.dataset.id, title: r.dataset.title, artist: r.dataset.artist, src: r.dataset.src, cover: r.dataset.cover }));
  }
  function setShuf(v) { shuf = v; const b = $('[data-act=shuf]'); if (b) b.classList.toggle('on', v); }

  if ('mediaSession' in navigator) {
    const ms = navigator.mediaSession;
    ms.setActionHandler('play', toggle); ms.setActionHandler('pause', toggle);
    ms.setActionHandler('nexttrack', next); ms.setActionHandler('previoustrack', prev);
  }
  document.addEventListener('click', e => {
    const a = e.target.closest('[data-act]');
    if (a) {
      const k = a.dataset.act;
      if (k === 'toggle') toggle(); else if (k === 'next') next(); else if (k === 'prev') prev();
      else if (k === 'shuf') setShuf(!shuf);
      else if (k === 'rep') { rep = (rep + 1) % 3; a.classList.toggle('on', rep > 0); a.firstElementChild.className = 'bi ' + (rep === 2 ? 'bi-repeat-1' : 'bi-repeat'); }
      return;
    }
    const pa = e.target.closest('[data-playall]');
    if (pa) {
      const L = document.querySelector(pa.dataset.playall); if (!L) return;
      build(L); if (!q.length) return;
      setShuf(pa.dataset.shuffle === '1');
      load(shuf ? Math.floor(Math.random() * q.length) : 0); return;
    }
    const p = e.target.closest('[data-play]');
    if (p) {
      const row = p.closest('.track'), L = row.closest('[data-tracklist]');
      if (cur() && cur().id === row.dataset.id && h) { toggle(); return; }
      build(L); load([...L.querySelectorAll('.track')].indexOf(row)); return;
    }
    const lk = e.target.closest('[data-like]');
    if (lk) {
      fetch(lk.dataset.url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(d => { lk.querySelector('i').className = 'bi ' + (d.liked ? 'bi-heart-fill text-acc' : 'bi-heart'); });
    }
  });
  document.addEventListener('input', e => {
    if (e.target.id === 'pSeek' && h) h.seek(h.duration() * e.target.value / 1000);
    if (e.target.id === 'pVol') { vol = +e.target.value; localStorage.setItem('sdVol', vol); if (h) h.volume(vol); }
  });
  document.addEventListener('keydown', e => {
    if (e.code === 'Space' && !/INPUT|TEXTAREA|SELECT|BUTTON/.test(e.target.tagName)) { e.preventDefault(); toggle(); }
  });
  setInterval(() => {
    if (!h) return; const s = h.seek(); if (typeof s !== 'number') return;
    const d = h.duration() || 0;
    if (document.activeElement.id !== 'pSeek') $('#pSeek').value = d ? s / d * 1000 : 0;
    $('#pNow').textContent = fmt(s); $('#pTotal').textContent = fmt(d);
  }, 300);

  document.addEventListener('turbo:load', () => {
    mark();
    const pv = $('#pVol'); if (pv) pv.value = vol;
    const el = document.querySelector('[data-sortable]');
    if (el) new Sortable(el, {
      handle: '.drag', animation: 150,
      onEnd: () => fetch(el.dataset.url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ order: [...el.children].map(r => +r.dataset.id) })
      })
    });
  });
  return { mark };
})();
</script>
</head>
<body>
<div class="shell">
  <aside class="side">
    <a href="{{ route('songs.index') }}" class="brand"><i class="bi bi-soundwave"></i> SOUNDORA</a>
    <nav class="vstack gap-1">
      <a class="nav-l {{ request()->routeIs('songs.index') && !request('liked') ? 'active' : '' }}" href="{{ route('songs.index') }}"><i class="bi bi-music-note-list"></i> Library</a>
      <a class="nav-l {{ request()->routeIs('albums.*') ? 'active' : '' }}" href="{{ route('albums.index') }}"><i class="bi bi-disc"></i> Album</a>
      <a class="nav-l {{ request('liked') ? 'active' : '' }}" href="{{ route('songs.index', ['liked' => 1]) }}"><i class="bi bi-heart"></i> Lagu Disukai</a>
      <a class="nav-l {{ request()->routeIs('playlists.index') ? 'active' : '' }}" href="{{ route('playlists.index') }}"><i class="bi bi-collection-play"></i> Playlist</a>
      <a class="nav-l {{ request()->routeIs('songs.create') ? 'active' : '' }}" href="{{ route('songs.create') }}"><i class="bi bi-plus-square"></i> Tambah Lagu</a>
    </nav>
    <div class="pl-list mt-4 vstack gap-1">
      <small class="text-secondary px-2 mb-1">Playlist kamu</small>
      @foreach (\App\Models\Playlist::orderBy('name')->get() as $pl)
        <a class="nav-l py-1 {{ request()->is('playlists/'.$pl->id) ? 'active' : '' }}" href="{{ route('playlists.show', $pl) }}">
          <span class="mini" style="background:{{ \App\Models\Song::gradient($pl->id) }}"></span><span class="text-truncate">{{ $pl->name }}</span>
        </a>
      @endforeach
    </div>
  </aside>

  <main class="main">
    @if (session('ok'))<div class="alert alert-success py-2" role="status">{{ session('ok') }}</div>@endif
    @if ($errors->any())
      <div class="alert alert-danger py-2" role="alert"><ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
  </main>

  <footer id="player" data-turbo-permanent>
    <div class="d-flex align-items-center gap-3 min-w-0">
      <div id="pCover"></div>
      <div class="min-w-0"><div id="pTitle" class="fw-bold text-truncate">Belum ada lagu</div><small id="pArtist" class="text-secondary">Pilih lagu untuk diputar</small></div>
    </div>
    <div>
      <div class="d-flex justify-content-center align-items-center gap-3">
        <button class="pbtn" data-act="shuf" title="Acak"><i class="bi bi-shuffle"></i></button>
        <button class="pbtn" data-act="prev" title="Sebelumnya"><i class="bi bi-skip-start-fill"></i></button>
        <button class="pbtn" data-act="toggle" title="Putar / jeda (Spasi)"><i id="pPlay" class="bi bi-play-circle-fill"></i></button>
        <button class="pbtn" data-act="next" title="Berikutnya"><i class="bi bi-skip-end-fill"></i></button>
        <button class="pbtn" data-act="rep" title="Ulangi"><i class="bi bi-repeat"></i></button>
      </div>
      <div class="d-flex align-items-center gap-2"><small id="pNow" class="text-secondary">0:00</small><input id="pSeek" type="range" class="seek" min="0" max="1000" value="0"><small id="pTotal" class="text-secondary">0:00</small></div>
    </div>
    <div class="vol d-flex align-items-center justify-content-end gap-2"><i class="bi bi-volume-up text-secondary"></i><input id="pVol" type="range" class="seek" style="max-width:110px" min="0" max="1" step="0.02" value="0.8"></div>
  </footer>
</div>
</body>
</html>
