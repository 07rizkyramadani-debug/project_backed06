<?php

namespace Modules\Catalog\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Catalog\ValueObjects\Isbn;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'isbn' => [
                'required', 'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (!Isbn::isValid((string) $value)) {
                        $fail('Format ISBN tidak valid (harus ISBN-10 atau ISBN-13 dengan check digit yang benar).');
                    } elseif (\Modules\Catalog\Models\Book::withTrashed()
                        ->where('isbn', Isbn::normalize((string) $value))
                        ->when($this->route('book'), fn ($q, $b) => $q->where('id', '!=', is_object($b) ? $b->id : $b))
                        ->exists()) {
                        $fail('ISBN sudah terdaftar.');
                    }
                },
            ],
            'publisher_id' => ['nullable', 'exists:publishers,id'],
            'published_year' => ['nullable', 'integer', 'between:1000,' . date('Y')],
            'total_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'author_ids' => ['required', 'array', 'min:1'],
            'author_ids.*' => ['exists:authors,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:book_categories,id'],
        ];
    }
}
