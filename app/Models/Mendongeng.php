<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Mendongeng extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'lokasi',
        'tgl',
        'deskripsi',
        'gambar',
        'partner',
        'jenis',
        'status',
        'gmap_link',
        'udangan_id',
        'exp_req',
        'st_req',
    ];

    public function partisipans()
    {
        return $this->hasMany(Partisipan::class, 'mendongeng_id', 'id');
    }

    public function undangan()
    {
        return $this->belongsTo(Undangan::class, 'udangan_id', 'id');
    }

    public function getGambarAttribute($value)
    {
        return url(Storage::url($value));
    }
}
