<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'user_id', 'post_id', 'parent_id', 'hidden'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comments::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comments::class, 'parent_id');
    }
}
