<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SongController::class, 'index'])->name('songs.index');
Route::get('/songs/create', [SongController::class, 'create'])->name('songs.create');
Route::post('/songs', [SongController::class, 'store'])->name('songs.store');
Route::get('/songs/{song}/edit', [SongController::class, 'edit'])->name('songs.edit');
Route::put('/songs/{song}', [SongController::class, 'update'])->name('songs.update');
Route::delete('/songs/{song}', [SongController::class, 'destroy'])->name('songs.destroy');
Route::post('/songs/{song}/like', [SongController::class, 'like'])->name('songs.like');

Route::resource('playlists', PlaylistController::class)->except(['create', 'edit']);
Route::post('/playlists/{playlist}/songs/{song}', [PlaylistController::class, 'addSong'])->name('playlists.songs.add');
Route::delete('/playlists/{playlist}/songs/{song}', [PlaylistController::class, 'removeSong'])->name('playlists.songs.remove');
Route::post('/playlists/{playlist}/reorder', [PlaylistController::class, 'reorder'])->name('playlists.reorder');

Route::get('/albums', [AlbumController::class, 'index'])->name('albums.index');
Route::get('/albums/{album}', [AlbumController::class, 'show'])->name('albums.show');
Route::post('/albums/{album}/playlist/{playlist}', [AlbumController::class, 'addToPlaylist'])->name('albums.playlist');
Route::get('/artists/{artist}', [ArtistController::class, 'show'])->name('artists.show');
