<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class friend extends Model
{
    protected $table = 'friends';
    
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function follower() {
        return $this->belongsTo('App\User', 'follower_id');
    }
}
