<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $guarded = [];

    public function songs()
    {
        return $this->belongsToMany(Song::class)
            ->withPivot('position')
            ->orderBy('playlist_song.position');
    }
}
