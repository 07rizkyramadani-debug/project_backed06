<?php

namespace Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Catalog\Models\Author;
use Modules\Catalog\Models\Book;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Publisher;
use Modules\Catalog\Requests\StoreBookRequest;
use Modules\Catalog\Requests\UpdateBookRequest;
use Modules\Catalog\ValueObjects\Isbn;

class BookController extends Controller
{
    public function index(Request $request)
    {
        // Eager loading: authors, publisher, categories dimuat sekaligus (menghindari N+1)
        $books = Book::with(['authors', 'publisher', 'categories'])
            ->when($request->filled('q'), fn ($q) => $q->search($request->string('q')->toString()))
            ->when($request->filled('category'), fn ($q) => $q->inCategory($request->integer('category')))
            ->when($request->boolean('available'), fn ($q) => $q->available())
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('library.books.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        $book->load(['authors', 'publisher', 'categories.parent']);

        return view('library.books.show', compact('book'));
    }

    public function create()
    {
        return view('library.books.form', $this->formData(new Book()));
    }

    public function store(StoreBookRequest $request)
    {
        $data = $request->validated();

        $book = Book::create([
            ...collect($data)->except(['author_ids', 'category_ids'])->all(),
            'isbn' => new Isbn($data['isbn']),
            'stock' => $data['total_stock'],
            'is_active' => true,
        ]);

        $book->authors()->sync($data['author_ids']);
        $book->categories()->sync($this->categoryPivot($data['category_ids'] ?? []));

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book)
    {
        $book->load(['authors', 'categories']);

        return view('library.books.form', $this->formData($book));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $data = $request->validated();

        // Selisih total stok dipertahankan agar stok tersedia tetap konsisten
        $diff = $data['total_stock'] - $book->total_stock;

        $book->update([
            ...collect($data)->except(['author_ids', 'category_ids'])->all(),
            'isbn' => new Isbn($data['isbn']),
            'stock' => max(0, $book->stock + $diff),
        ]);

        $book->authors()->sync($data['author_ids']);
        $book->categories()->sync($this->categoryPivot($data['category_ids'] ?? []));

        return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui.');
    }

    /** Soft delete */
    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku dipindahkan ke daftar terhapus
         (soft delete).');
    }

    /** Daftar buku yang di-soft delete (admin) */
    public function trashed()
    {
        $books = Book::onlyTrashed()->with(['authors', 'publisher'])->latest('deleted_at')->paginate(10);

        return view('library.books.trashed', compact('books'));
    }

    /** Restore buku (admin) */
    public function restore(int $id)
    {
        Book::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('books.trashed')->with('success', 'Buku berhasil dipulihkan.');
    }

    private function formData(Book $book): array
    {
        return [
            'book' => $book,
            'authors' => Author::orderBy('name')->get(['id', 'name']),
            'publishers' => Publisher::orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ];
    }

    /** Siapkan data pivot (added_at) untuk relasi many-to-many buku-kategori. */
    private function categoryPivot(array $categoryIds): array
    {
        return collect($categoryIds)
            ->mapWithKeys(fn ($id) => [$id => ['added_at' => now()]])
            ->all();
    }
}

