<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'title', 'content', 'category_id', 'image'
    ];
    // Relación de uno a muchos inversa (muchos a uno)

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function category() {
        return $this->belongsTo('App\Category', 'category_id');
    }

    public function ratings() {
        return $this->hasMany('App\Rating');
    }

    public function comments() {
        return $this->hasMany('App\Comment');
    }
}
