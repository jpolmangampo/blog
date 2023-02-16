<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;
    public $timestamps = false;

    // To get the info of the follower
    public function follower(){
        return $this->belongsTo(User::class, 'follower_id')->withTrashed();
    }

    // to get the infoof the user being followed
    public function following(){
        return $this->belongsTo(User::class, 'following_id')->withTrashed();
    }


}
