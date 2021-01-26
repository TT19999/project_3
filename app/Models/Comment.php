<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public function set(){
        return $this->belongsTo(Set::class);
    }
    protected $fillable=[
        'post_id',
        'comment',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
