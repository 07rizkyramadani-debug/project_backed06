<?php

namespace Modules\Catalog\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Modules\Catalog\ValueObjects\Isbn;

/**
 * Menyimpan ISBN sebagai string ternormalisasi di database,
 * dan mengembalikannya sebagai Value Object Isbn di model.
 */
class IsbnCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Isbn
    {
        return $value === null ? null : new Isbn($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return ($value instanceof Isbn ? $value : new Isbn((string) $value))->getValue();
    }
}

