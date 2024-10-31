<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Article extends Model
{
    use HasFactory;


    protected $fillable = ['title', 'img', 'content', 'user_id', 'status'];


    public function sender() : BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // رابطه many-to-many با دسته‌بندی‌ها
    public function categories() : BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'article_category');
    }

    public function images()
    {
        return $this->hasMany(ArticleImage::class);
    }


    public function scopeOwner(Builder $query)
    {
        return $query->where('creator_id', Auth::id());
    }
}
