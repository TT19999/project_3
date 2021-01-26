<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable=[
        'set_id',
        'word',
        'lexical',
        'example',
        'image',
        'meaning',
        'phonetic',
        'audioSrc',
    ];

    public function set()
    {
        return $this->belongsTo(Set::class);
    }
}
