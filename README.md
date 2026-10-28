# SOUNDORA – Aplikasi Pemutar Musik Berbasis Laravel

SOUNDORA adalah aplikasi pemutar musik bergaya Spotify untuk penggunaan pribadi. Pengguna dapat mengelola lagu, album, dan artis, membuat playlist sendiri, lalu memutar lagu dengan pemutar yang tetap berjalan saat berpindah halaman.

Dokumen ini menjelaskan **fitur**, **struktur kode**, dan **fungsi tiap bagian**.

---

## 1. Teknologi yang dipakai

| Bagian         | Teknologi               | Fungsi                                                      |
| -------------- | ----------------------- | ----------------------------------------------------------- |
| Backend        | Laravel 11/12 (PHP)     | Routing, validasi, Eloquent ORM, Artisan                    |
| Database       | SQLite/MySQL (bebas)    | Menyimpan artis, album, lagu, playlist                      |
| Tampilan       | Blade + Bootstrap 5.3.3 | Halaman, grid, modal, dropdown                              |
| Ikon           | Bootstrap Icons 1.11.3  | Ikon tombol dan menu                                        |
| Pemutar audio  | Howler.js 2.2.4 (CDN)   | Memutar lagu, volume, seek                                  |
| Navigasi mulus | Hotwired Turbo 8 (CDN)  | Pindah halaman tanpa reload sehingga musik tidak terputus   |
| Drag & drop    | SortableJS 1.15.2 (CDN) | Mengubah urutan lagu dalam playlist                         |
| Baca tag lagu  | getID3 (Composer)       | Membaca ID3 (judul, artis, album, sampul) saat impor folder |

---

## 2. Ringkasan fitur

**Library & lagu**

- Menampilkan semua lagu dengan sampul, artis, album, durasi.
- Pencarian berdasarkan judul, nama artis, atau nama album.
- Tambah lagu lewat unggah file audio **atau** tautan audio, plus sampul opsional.
- Edit lagu (judul, artis, album, sampul) dan ganti file audio.
- Hapus lagu dengan konfirmasi; file lokal ikut dihapus dari penyimpanan.
- Suka/tidak suka lagu (ikon hati) dan halaman **Lagu Disukai**.
- Durasi lagu dibaca otomatis dari file saat unggah.
- Lencana **"30 dtk"** pada lagu yang hanya berupa cuplikan.

**Playlist (fitur utama tugas)**

- Membuat playlist (nama + deskripsi), melihat daftar, mengubah, menghapus.
- Menambah lagu ke playlist dari dua tempat: jendela pencarian di halaman playlist, atau menu ⋯ pada lagu.
- Mengeluarkan lagu dari playlist **tanpa menghapus lagu aslinya**.
- Menghapus playlist **tanpa menghapus lagunya**.
- Mengubah urutan lagu dengan drag & drop; urutan tersimpan di database.
- Satu playlist bisa berisi banyak lagu; satu lagu bisa ada di banyak playlist (many-to-many).

**Album & artis**

- Halaman daftar album dengan sampul dan pencarian.
- Halaman detail album: sampul, artis, tahun, genre, jumlah dan total durasi lagu.
- **Simpan semua lagu album ke playlist** dengan satu klik (lagu yang sudah ada dilewati).
- Halaman artis: semua album dan semua lagunya.

**Pemutar musik (bar di bawah layar)**

- Putar/jeda, lagu sebelumnya, lagu berikutnya, acak (shuffle), ulangi (mati / semua / satu lagu).
- Bar kemajuan (seek) dan pengatur volume; volume diingat di browser.
- Lagu berikutnya otomatis diputar saat lagu selesai.
- Tombol **Putar semua** dan **Acak** pada setiap daftar (library, playlist, album, artis).
- Pemutar tetap berjalan saat berpindah halaman.
- Pintasan keyboard: **Spasi** untuk putar/jeda.
- Mendukung tombol media browser/layar kunci (Media Session API).
- Jika lagu gagal dimuat: muncul pemberitahuan lalu otomatis lompat ke lagu berikutnya.

**Impor lagu (perintah Artisan)**

- Impor dari folder musik lokal beserta album dan foto.
- Impor katalog cuplikan dari iTunes Search API.
- (Opsional) impor/unduh lagu Creative Commons dari Jamendo.

---

## 3. Struktur proyek

```
app/
 ├─ Console/Commands/
 │   ├─ ScanMusic.php          soundora:scan
 │   ├─ ImportMusic.php        soundora:import
 │   ├─ ImportJamendo.php      soundora:jamendo
 │   └─ DownloadJamendo.php    soundora:jamendo-download
 ├─ Http/Controllers/
 │   ├─ SongController.php
 │   ├─ PlaylistController.php
 │   ├─ AlbumController.php
 │   └─ ArtistController.php
 └─ Models/  Artist, Album, Song, Playlist
database/
 ├─ migrations/  (3 file)
 └─ seeders/DatabaseSeeder.php
resources/views/
 ├─ layouts/app.blade.php      kerangka halaman + seluruh JavaScript pemutar
 ├─ partials/track.blade.php   satu baris lagu (dipakai di semua daftar)
 ├─ partials/playlist-modal.blade.php   form buat/edit playlist
 ├─ songs/     index, create (juga dipakai untuk edit)
 ├─ playlists/ index, show
 ├─ albums/    index, show
 └─ artists/   show
routes/web.php
```

---

## 4. Struktur database

```
artists ──< albums ──< songs >──< playlist_song >──< playlists
   └──────────────────< songs
```

| Tabel           | Kolom penting                                                                        | Keterangan                                                                                            |
| --------------- | ------------------------------------------------------------------------------------ | ----------------------------------------------------------------------------------------------------- |
| `artists`       | `name` (unik)                                                                        | Satu baris per artis                                                                                  |
| `albums`        | `artist_id`, `name`, `cover_path`, `year`, `genre`                                   | `artist_id` boleh kosong (nullOnDelete)                                                               |
| `songs`         | `title`, `artist_id`, `album_id`, `audio_path`, `cover_path`, `duration`, `is_liked` | `audio_path` berisi path file lokal **atau** URL penuh; `duration` dalam detik                        |
| `playlists`     | `name` (maks 60), `description` (maks 255)                                           |                                                                                                       |
| `playlist_song` | `playlist_id`, `song_id`, `position`                                                 | Tabel penghubung many-to-many; pasangan `playlist_id + song_id` **unik**; `position` menyimpan urutan |

Aturan hapus (foreign key):

- Hapus lagu → barisnya di `playlist_song` ikut terhapus (cascade).
- Hapus playlist → relasinya terhapus, lagu tetap ada.
- Hapus album → `album_id` pada lagu menjadi kosong, lagu tetap ada.
- Hapus artis → lagu milik artis itu ikut terhapus.

### Migration

| File                             | Fungsi                                                  |
| -------------------------------- | ------------------------------------------------------- |
| `…000001_create_soundora_tables` | Membuat kelima tabel di atas                            |
| `…000002_add_album_details`      | Menambah `cover_path`, `year`, `genre` pada `albums`    |
| `…000003_mark_preview_durations` | Mengoreksi durasi lagu cuplikan iTunes menjadi 30 detik |

---

## 5. Model

| Model      | Relasi                                                                          | Method/accessor penting                        |
| ---------- | ------------------------------------------------------------------------------- | ---------------------------------------------- |
| `Artist`   | `songs()` hasMany, `albums()` hasMany                                           | –                                              |
| `Album`    | `artist()` belongsTo, `songs()` hasMany                                         | `cover_css`: nilai CSS background sampul album |
| `Song`     | `artist()`, `album()` belongsTo; `playlists()` **belongsToMany** (+ `position`) | lihat tabel di bawah                           |
| `Playlist` | `songs()` **belongsToMany** (+ `position`, diurutkan per `position`)            | –                                              |

Fungsi pada `Song`:

| Fungsi                           | Kegunaan                                                                                 |
| -------------------------------- | ---------------------------------------------------------------------------------------- |
| `isRemote()`                     | `true` jika `audio_path` berupa URL (http/https)                                         |
| `isPreview()`                    | `true` jika audionya cuplikan iTunes (30 detik)                                          |
| `audio_url`                      | URL yang dipakai pemutar: file lokal lewat disk `public`, atau URL apa adanya            |
| `cover_css` / `Song::coverCss()` | Menghasilkan `background` CSS: gambar sampul (lokal/URL) dengan gradasi sebagai cadangan |
| `duration_label`                 | Mengubah detik menjadi `m:ss` (atau `—` jika 0)                                          |
| `Song::gradient($id)`            | Gradasi warna unik per lagu/album/playlist/artis jika tidak punya gambar                 |

---

## 6. Route

| Method | URL                                   | Nama                   | Fungsi                                  |
| ------ | ------------------------------------- | ---------------------- | --------------------------------------- |
| GET    | `/`                                   | songs.index            | Library, pencarian, filter lagu disukai |
| GET    | `/songs/create`                       | songs.create           | Form tambah lagu                        |
| POST   | `/songs`                              | songs.store            | Simpan lagu baru                        |
| GET    | `/songs/{song}/edit`                  | songs.edit             | Form edit lagu                          |
| PUT    | `/songs/{song}`                       | songs.update           | Simpan perubahan lagu                   |
| DELETE | `/songs/{song}`                       | songs.destroy          | Hapus lagu                              |
| POST   | `/songs/{song}/like`                  | songs.like             | Ubah status suka (JSON)                 |
| GET    | `/playlists`                          | playlists.index        | Daftar playlist                         |
| POST   | `/playlists`                          | playlists.store        | Buat playlist                           |
| GET    | `/playlists/{playlist}`               | playlists.show         | Detail playlist                         |
| PUT    | `/playlists/{playlist}`               | playlists.update       | Ubah nama/deskripsi                     |
| DELETE | `/playlists/{playlist}`               | playlists.destroy      | Hapus playlist                          |
| POST   | `/playlists/{playlist}/songs/{song}`  | playlists.songs.add    | Tambah lagu ke playlist                 |
| DELETE | `/playlists/{playlist}/songs/{song}`  | playlists.songs.remove | Keluarkan lagu dari playlist            |
| POST   | `/playlists/{playlist}/reorder`       | playlists.reorder      | Simpan urutan baru (JSON)               |
| GET    | `/albums`                             | albums.index           | Daftar album                            |
| GET    | `/albums/{album}`                     | albums.show            | Detail album                            |
| POST   | `/albums/{album}/playlist/{playlist}` | albums.playlist        | Simpan seluruh album ke playlist        |
| GET    | `/artists/{artist}`                   | artists.show           | Halaman artis                           |

---

## 7. Controller

### SongController

| Method                  | Fungsi                                                                                                                                                              |
| ----------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index`                 | Mengambil lagu (dengan artis & album), menerapkan pencarian `q` (judul/artis/album) dan filter `liked`                                                              |
| `create` / `edit`       | Menampilkan form (view yang sama); `edit` mengisi data lama                                                                                                         |
| `store`                 | Validasi (judul, artis wajib; audio file **atau** URL wajib salah satu; sampul gambar; durasi), membuat artis/album bila belum ada, menyimpan file ke disk `public` |
| `update`                | Sama seperti `store`, tetapi audio/sampul opsional; file lama dihapus jika diganti                                                                                  |
| `destroy`               | Menghapus file audio & sampul lokal lalu barisnya                                                                                                                   |
| `like`                  | Membalik `is_liked`, mengembalikan JSON                                                                                                                             |
| `artistAlbum` (private) | `firstOrCreate` artis dan album dari input teks                                                                                                                     |
| `removeFile` (private)  | Menghapus file hanya jika lokal; URL eksternal tidak disentuh                                                                                                       |

### PlaylistController

| Method             | Fungsi                                                                             |
| ------------------ | ---------------------------------------------------------------------------------- |
| `index`            | Daftar playlist beserta jumlah lagu                                                |
| `store` / `update` | Validasi nama (wajib, maks 60) dan deskripsi (maks 255)                            |
| `show`             | Playlist + lagunya + daftar lagu yang **belum** ada (untuk jendela tambah lagu)    |
| `destroy`          | Menghapus playlist saja; lagu aman                                                 |
| `addSong`          | `syncWithoutDetaching` sehingga lagu tidak dobel; `position` diisi urutan terakhir |
| `removeSong`       | `detach` — hanya memutus relasi, lagu asli tidak terhapus                          |
| `reorder`          | Menerima daftar ID lagu berurutan, memperbarui `position` setiap lagu              |

### AlbumController

| Method          | Fungsi                                                                                                    |
| --------------- | --------------------------------------------------------------------------------------------------------- |
| `index`         | Album yang punya lagu, dengan pencarian album/artis                                                       |
| `show`          | Detail album + lagu + daftar playlist untuk menu "Simpan semua"                                           |
| `addToPlaylist` | Memasukkan semua lagu album ke playlist, melewati yang sudah ada, lalu melaporkan jumlah yang ditambahkan |

### ArtistController

| Method | Fungsi                                       |
| ------ | -------------------------------------------- |
| `show` | Artis + album (terbaru dulu) + semua lagunya |

---

## 8. Tampilan (Blade)

| View                          | Fungsi                                                                                                                                                                                                                                       |
| ----------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `layouts/app`                 | Sidebar (Library, Album, Lagu Disukai, Playlist, Tambah Lagu, daftar playlist), area konten, pesan sukses/error, bar pemutar, dan seluruh JavaScript pemutar                                                                                 |
| `partials/track`              | Satu baris lagu: tombol putar, sampul, judul, artis & album (tautan), ikon suka, durasi, menu ⋯. Di dalam playlist menampilkan pegangan drag dan "Keluarkan dari playlist"; di tempat lain menampilkan "Tambah ke playlist" dan "Hapus lagu" |
| `partials/playlist-modal`     | Satu modal untuk buat **dan** edit playlist                                                                                                                                                                                                  |
| `songs/index`                 | Library: judul, pencarian, Putar semua, Acak                                                                                                                                                                                                 |
| `songs/create`                | Form tambah **dan** edit lagu; menghitung durasi otomatis dari file/URL; menampilkan peringatan jika lagu hanya cuplikan                                                                                                                     |
| `playlists/index`             | Grid playlist + tombol playlist baru                                                                                                                                                                                                         |
| `playlists/show`              | Header playlist, aksi (putar, acak, tambah lagu, edit, hapus), daftar lagu yang bisa di-drag, jendela pencarian tambah lagu                                                                                                                  |
| `albums/index`, `albums/show` | Grid album; detail album dengan "Simpan semua ke playlist"                                                                                                                                                                                   |
| `artists/show`                | Header artis, grid album, semua lagu                                                                                                                                                                                                         |

Setiap baris lagu membawa data (`data-id`, `data-title`, `data-artist`, `data-src`, `data-cover`) yang dibaca JavaScript untuk membangun antrean pemutaran. Form hapus memakai `data-turbo-confirm` untuk dialog konfirmasi.

---

## 9. JavaScript pemutar (`window.SD` di `layouts/app`)

Pemutar berada di elemen `#player` dengan `data-turbo-permanent`, sehingga tidak dibuat ulang saat pindah halaman dan musik tetap berjalan.

| Fungsi        | Kegunaan                                                                                                            |
| ------------- | ------------------------------------------------------------------------------------------------------------------- |
| `build(list)` | Membuat antrean dari semua `.track` dalam daftar (`[data-tracklist]`) dan mereset riwayat                           |
| `load(n)`     | Memutar lagu ke-`n`: melepas event lagu lama, membuat objek Howl baru, menetapkan format audio                      |
| `next()`      | Lagu berikutnya; mengikuti mode acak dan ulangi (berhenti di akhir jika ulangi mati)                                |
| `prev()`      | Jika lagu sudah berjalan >3 detik, kembali ke awal; jika tidak, ke lagu yang diputar sebelumnya (riwayat hingga 50) |
| `toggle()`    | Putar/jeda                                                                                                          |
| `skip()`      | Dipanggil saat lagu gagal dimuat; lompat ke berikutnya, berhenti jika semua lagu gagal                              |
| `ui()`        | Memperbarui judul, artis, sampul, ikon putar, dan metadata Media Session                                            |
| `mark()`      | Menandai baris lagu yang sedang diputar dan mengganti ikon play/pause di daftar                                     |
| `toast()`     | Pemberitahuan singkat (mis. gagal memuat lagu)                                                                      |
| `extOf()`     | Menentukan format dari ekstensi URL (cadangan `mp3`)                                                                |

Penanganan event:

- **Klik** memakai delegasi pada `document`: `data-act` (tombol bar pemutar), `data-playall` (Putar semua/Acak), `data-play` (putar satu lagu), `data-like` (suka, memakai `fetch` + token CSRF).
- **Input**: bar seek dan volume (volume disimpan di `localStorage`).
- **Keyboard**: Spasi untuk putar/jeda (diabaikan saat mengetik di kolom input).
- **Timer** 300 ms memperbarui bar kemajuan dan waktu.
- **`turbo:load`**: menandai lagu yang sedang diputar di halaman baru, memulihkan volume, dan mengaktifkan drag & drop (SortableJS) pada playlist yang membawa `data-sortable`; setelah drag, urutan dikirim ke `/playlists/{id}/reorder`.

Perlindungan bug yang sudah ditangani:

- Event lagu lama dilepas (`off`) dan dijaga token agar tidak memicu lompat ganda.
- `onplayerror` mencoba memutar ulang setelah browser mengizinkan audio (autoplay diblokir).
- `onloaderror` memberi tahu pengguna dan lanjut ke lagu berikutnya.

---

## 10. Perintah Artisan

| Perintah                                                                            | Fungsi                                                                                                                                                                                                                                                                                                                                                                                                                           |
| ----------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `php artisan soundora:scan {path} [--clean]`                                        | Mengimpor file audio dari folder (mp3, m4a, wav, ogg, flac). Membaca tag ID3; jika kosong memakai struktur folder `Artis/Album/NN - Judul.mp3`. Foto album dari tag atau `cover.jpg`/`folder.jpg`/`front.jpg`; foto lagu dari gambar bernama sama dengan file lagu. Lagu yang sudah punya file lengkap dilewati; lagu cuplikan dengan judul+artis sama diganti file lengkap. `--clean` menghapus lagu cuplikan/contoh lebih dulu |
| `php artisan soundora:import [artis…] [--theme=] [--limit=] [--country=] [--fresh]` | Mengisi katalog dari iTunes Search API: judul, artis, album, tahun, sampul, dan **cuplikan audio 30 detik**                                                                                                                                                                                                                                                                                                                      |
| `php artisan soundora:jamendo [--tag=] [--limit=] [--clear-previews]`               | (Opsional) Impor lagu Creative Commons berdurasi penuh dari Jamendo; butuh `JAMENDO_CLIENT_ID`                                                                                                                                                                                                                                                                                                                                   |
| `php artisan soundora:jamendo-download {path} [--tag=]`                             | (Opsional) Mengunduh lagu Jamendo ke folder dengan struktur Artis/Album                                                                                                                                                                                                                                                                                                                                                          |

Struktur folder yang dikenali `soundora:scan`:

```
D:\Musik\Nama Artis\Nama Album\cover.jpg
D:\Musik\Nama Artis\Nama Album\01 - Judul.mp3
D:\Musik\Nama Artis\Nama Album\01 - Judul.jpg   (foto khusus lagu, opsional)
```

---

## 11. Seeder

`DatabaseSeeder` membuat 6 lagu contoh (dari SoundHelix, perlu internet), beberapa artis & album, dan satu playlist **"Chill Pills"**. Gunanya agar aplikasi langsung terlihat berfungsi setelah `migrate --seed`; lagu contoh bisa dihapus dengan opsi `--clean`/`--fresh`.

---

## 12. Keamanan & kualitas

- **CSRF**: semua form memakai `@csrf`; permintaan `fetch` mengirim `X-CSRF-TOKEN`.
- **Validasi** di server: tipe file audio (mp3, mpga, wav, ogg, m4a, aac, flac, maks 30 MB), gambar (maks 3 MB), URL, panjang teks.
- **Mass assignment**: model memakai `$guarded = []` karena semua input melewati validasi controller.
- **Penghapusan aman**: dialog konfirmasi menjelaskan dampaknya; hanya file lokal yang dihapus dari disk.
- **Integritas data**: foreign key + cascade, dan kolom unik pada `playlist_song`.
- **Output** di Blade memakai `{{ }}` sehingga otomatis di-escape dari XSS.

---

## 13. Pemetaan ke ketentuan tugas

| Ketentuan tugas                                                               | Implementasi                                          |
| ----------------------------------------------------------------------------- | ----------------------------------------------------- |
| Tabel `playlists` & `playlist_song`                                           | Migration `…000001`                                   |
| Model Playlist dengan relasi many-to-many ke Song                             | `Playlist::songs()`, `Song::playlists()`              |
| `PlaylistController`                                                          | Section 7                                             |
| Route tampil, buat, ubah, hapus, kelola isi                                   | Section 6                                             |
| View daftar, form, detail (Blade + Bootstrap 5.3.3)                           | `playlists/index`, `playlist-modal`, `playlists/show` |
| Howler.js via CDN                                                             | `layouts/app`                                         |
| Buat playlist (nama + deskripsi)                                              | `PlaylistController@store`                            |
| Tampilkan daftar playlist                                                     | `PlaylistController@index`                            |
| Tambah lagu ke playlist                                                       | `addSong`                                             |
| Putar lagu dari playlist + otomatis lanjut                                    | Tombol putar + `next()` pada `onend`                  |
| Ubah nama, hapus playlist, hapus lagu dari playlist tanpa menghapus lagu asli | `update`, `destroy`, `removeSong`                     |

**Fitur tambahan di luar tugas:** album & artis, suka, acak/ulangi, drag & drop urutan, simpan seluruh album ke playlist, pencarian, impor dari folder, Media Session, navigasi tanpa reload.

---

## 14. Batasan yang perlu diketahui

- **Tanpa login**: semua data dipakai bersama (cocok untuk penggunaan pribadi).
- **Lagu hasil `soundora:import` hanya cuplikan 30 detik** — itu batas dari iTunes, bukan dari pemutar. Lagu penuh memerlukan file milik sendiri (`soundora:scan` atau menu Edit / ganti audio).
- Library memuat semua lagu sekaligus (tanpa halaman/pagination); nyaman untuk beberapa ratus lagu.
- Navigasi mulus dan pemutar memerlukan koneksi internet untuk memuat library dari CDN.
- Ukuran unggahan dibatasi `upload_max_filesize` dan `post_max_size` di `php.ini`.
- Kode belum diuji menyeluruh di lingkungan pengembangan; uji setiap fitur sebelum dikumpulkan.
