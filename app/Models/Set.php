<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'intro',
        'cover',
        'status',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withCount('sets');
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class)->with("user:id,avatar,name")->orderBy("created_at","desc");
    }
}
