# SOUNDORA – Aplikasi Musik + Fitur Playlist (Laravel)

Isi folder ini adalah "overlay": salin ke dalam proyek Laravel 11/12 baru (atau proyek SOUNDORA lamamu).

## Pasang
```bash
composer create-project laravel/laravel soundora
cd soundora
# salin semua isi folder ini ke dalam proyek (pilih "timpa/replace")
php artisan migrate --seed
php artisan storage:link
php artisan soundora:import --fresh   # isi lagu populer + album + sampul (butuh internet, ±2 menit)
php artisan serve
```
Buka http://127.0.0.1:8000. Butuh internet untuk CDN (Bootstrap 5.3.3, Howler.js, Turbo) dan lagu contoh.

Jika upload lagu gagal: naikkan `upload_max_filesize` dan `post_max_size` di php.ini (misal 32M).
Jika proyek lamamu sudah punya tabel/model Song/Artist/Album, sesuaikan nama kolom di migration & model.

## Struktur
- `database/migrations/…_create_soundora_tables.php` – artists, albums, songs, playlists, playlist_song (pivot + position)
- `app/Models` – Playlist ⇄ Song (many-to-many), Song → Artist/Album
- `app/Http/Controllers/PlaylistController.php` – CRUD playlist, tambah/keluarkan lagu, urutkan
- `routes/web.php` – semua route
- `resources/views` – layout + pemutar (Howler.js), library, daftar & detail playlist (Blade + Bootstrap 5.3.3)

## Cek untuk dikumpulkan
Source code, screenshot daftar & detail playlist, screenshot tambah & hapus lagu. Batas: 4 November 2026.

## Impor lagu populer
```bash
php artisan soundora:import                                   # daftar bawaan: Daniel Caesar, Billie Eilish, dst + tema funk
php artisan soundora:import "Daniel Caesar" "Tulus"           # artis pilihanmu
php artisan soundora:import --theme="funk 2026" --limit=50    # kata kunci bebas
php artisan soundora:import --country=ID                      # pakai toko iTunes Indonesia
```
Data diambil dari iTunes Search API. Audio yang diputar adalah cuplikan resmi 30 detik, bukan lagu penuh.
Untuk lagu penuh, upload file milikmu lewat menu Tambah Lagu.

## Kenapa lagu hanya 30 detik?
Lagu hasil `soundora:import` adalah cuplikan resmi 30 detik dari iTunes (ditandai lencana "30 dtk"). Itu batas dari sumbernya, bukan bug pemutar.
Untuk lagu penuh, pakai file milikmu sendiri:
- **Satu lagu:** menu ⋯ pada lagu → *Edit / ganti audio* → unggah file lengkap.
- **Banyak lagu sekaligus:**
```bash
composer require james-heinrich/getid3
php artisan soundora:scan "D:/Musik"        # baca tag ID3 (judul, artis, album, sampul) dari folder
```
Jika judul dan artis sama dengan lagu cuplikan, cuplikannya otomatis diganti file lengkap.

## Lagu durasi penuh (legal)
Cuplikan iTunes tidak bisa dijadikan lagu penuh. Sumber gratis yang menyediakan lagu penuh secara legal adalah Jamendo (musik Creative Commons).
1. Daftar di https://devportal.jamendo.com, buat aplikasi, salin **Client ID**.
2. Tambahkan ke `.env`: `JAMENDO_CLIENT_ID=isi_client_id_kamu`
3. Jalankan:
```bash
php artisan soundora:jamendo --clear-previews                 # genre bawaan: funk, pop, rock, hiphop, rnb, soul, lofi, jazz, ...
php artisan soundora:jamendo --tag=funk --tag=lofi --limit=100
```
Semua lagu hasilnya berdurasi penuh, lengkap dengan album dan sampul.
Untuk lagu artis populer (Billie Eilish, Daniel Caesar, dst), pakai file milikmu lewat `soundora:scan` atau menu Edit / ganti audio.

## Folder musik di D: (struktur) + unduh sekaligus
```
D:\Musik\
 └─ Nama Artis\
     └─ Nama Album\
         ├─ cover.jpg
         ├─ 01 - Judul Lagu.mp3
         └─ 02 - Judul Lagu.mp3
```
Unduh lagu legal (Jamendo, Creative Commons) langsung ke struktur itu, lalu impor ke SOUNDORA:
```bash
php artisan soundora:jamendo-download "D:/Musik" --tag=funk --tag=lofi --limit=30
php artisan soundora:scan "D:/Musik"
```
`soundora:scan` membaca tag ID3; bila kosong, artis/album/judul diambil dari nama folder dan file, dan cover.jpg dipakai sebagai sampul.

## Pakai lagu milikmu di D:/Musik
```
D:\Musik\Nama Artis\Nama Album\cover.jpg              <- foto album
D:\Musik\Nama Artis\Nama Album\01 - Judul.mp3
D:\Musik\Nama Artis\Nama Album\01 - Judul.jpg         <- foto khusus lagu (opsional, nama sama dengan lagunya)
```
```bash
composer require james-heinrich/getid3
php artisan storage:link
php artisan soundora:scan "D:/Musik" --clean     # --clean membuang lagu cuplikan/contoh, sisakan lagumu saja
```
Jalankan lagi kapan pun menambah lagu baru; lagu yang sudah ada dilewati.
