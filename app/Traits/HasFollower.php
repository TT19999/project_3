<?php
namespace App\Traits;

use App\Models\Follower;
use App\Models\Role;

trait HasFollower
{
    protected $followerList = null;

    public function follower()
    {
        return $this->hasMany(Follower::class);
    }

    public function hasFollower($id)
    {
        if (is_int($id)) {
            return $this->follower->contains('follower_id', $id);
        }
        return false;
    }

    public function getFollower(){
        return $this->follower;
    }
}
