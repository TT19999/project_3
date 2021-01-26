<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $hidden = [
        'pivot',
    ];
    public function sets(){
        return $this->belongsToMany(Set::class);
    }
}
