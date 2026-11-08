<?php

namespace Modules\Catalog\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Catalog\Casts\IsbnCast;
use Modules\Catalog\ValueObjects\Isbn;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'publisher_id', 'title', 'isbn', 'published_year',
        'total_stock', 'stock', 'is_active', 'description',
    ];

    protected $casts = [
        'isbn' => IsbnCast::class,
        'is_active' => 'boolean',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    /** Many-to-Many dengan pivot added_at */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'book_category'
        , 'book_id', 'category_id')
            ->withPivot('added_at');
    }

    // ---------- Query scopes ----------

    /** Buku yang masih tersedia untuk dipinjam */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('stock', '>', 0)->where('is_active', true);
    }

    /** Pencarian berdasarkan judul, ISBN, atau nama penulis */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
                ->orWhere('isbn', 'like', '%' . Isbn::normalize($keyword) . '%')
                ->orWhereHas('authors', fn (Builder $a) => $a->where('name'
                , 'like', "%{$keyword}%"));
        });
    }

    /** Filter berdasarkan kategori */
    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->whereHas(
            'categories',
            fn (Builder $q) => $q->where('book_categories.id', $categoryId)
        );
    }

    public function isInStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }

    protected static function newFactory(): BookFactory
    {
        return BookFactory::new();
    }
}

