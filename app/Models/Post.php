<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'content', 'cover_image', 'published', 'categories', 'summary', 'draft', 'author_id'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_posts');

}
public function subscriptions()
{
    return $this->hasMany(Subscription::class, 'author_id');
}


}
