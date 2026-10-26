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
