<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'category_id',
        'brief_intro',
        'content',
        'status_save_draft',
        'send_approval',
        'status_approval',
        'status_get_post'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function postImages()
    {
        return $this->hasMany(PostImages::class);
    }

    public function draft()
    {
        return $this->hasOne(Draft::class);
    }
}
