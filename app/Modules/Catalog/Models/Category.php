<?php

namespace Modules\Catalog\Models;

use Database\Factories\LibraryCategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $table = 'book_categories';

    protected $fillable = ['name', 'slug', 'description', 'parent_id'];

    /** Self-referencing: induk kategori */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /** Self-referencing: sub-kategori */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /** Many-to-Many dengan pivot added_at */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_category', 'category_id', 'book_id')
            ->withPivot('added_at');
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    protected static function newFactory(): LibraryCategoryFactory
    {
        return LibraryCategoryFactory::new();
    }
}

