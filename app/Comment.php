<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'Comments';
    protected $fillable = [
        'description', 'user_id',
    ];
    
    public function commentRatings() {
        return $this->hasMany('App\CommentRating');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
