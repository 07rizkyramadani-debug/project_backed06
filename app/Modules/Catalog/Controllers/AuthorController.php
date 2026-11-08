<?php

namespace Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use Modules\Catalog\Models\Author;

class AuthorController extends Controller
{
    public function index()
    {
        // Eager loading judul buku tiap penulis dalam 1 query tambahan
        $authors = Author::with('books:id,title')
            ->withCount('books')
            ->orderBy('name')
            ->paginate(15);

        return view('library.authors.index', compact('authors'));
    }
}
