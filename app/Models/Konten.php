<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Konten extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'judul',
        'gambar',
        'link',
        'deskripsi',
        'jenis',
        'status',
        'user_id',
    ];

    public function user()
    {
        # code... many konten to one user
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getGambarAttribute($value)
    {
        return url(Storage::url($value));
    }
}
