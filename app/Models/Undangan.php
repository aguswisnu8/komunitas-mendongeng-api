<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Undangan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'pengirim',
        'nm_kegiatan',
        'lokasi',
        'tgl',
        'deskripsi',
        'jenis',
        'penyelenggara',
        'contact',
        'status',
    ];

    public function mendongeng()
    {
        return $this->hasOne(Mendongeng::class, 'udangan_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
