<?php

namespace Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use Modules\Catalog\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        // Eager loading: sub-kategori + jumlah buku dihitung dalam query yang sama
        $categories = Category::roots()
            ->with(['children' => fn ($q) => $q->withCount('books')])
            ->withCount('books')
            ->orderBy('name')
            ->get();

        return view('library.categories.index', compact('categories'));
    }
}
