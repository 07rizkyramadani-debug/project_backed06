@php($editing = $book->exists)
<x-app-layout>
    @include('library._styles')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $editing ? 'Edit Buku' : 'Tambah Buku' }}</h2>
    </x-slot>

    <div style="padding: 2rem 0;">
        <div class="lib-wrap lib-w-3xl">
            @include('library._flash')

            <form method="POST" action="{{ $editing ? route('books.update', $book) : route('books.store') }}" class="lib-card lib-form">
                @csrf
                @if ($editing) @method('PUT') @endif

                <div class="lib-section-title">Informasi Buku</div>
                <div>
                    <label for="title">Judul</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $book->title) }}" placeholder="Contoh: Pemrograman Web dengan Laravel" required>
                </div>
                <div class="lib-grid-2">
                    <div>
                        <label for="isbn">ISBN</label>
                        <input id="isbn" name="isbn" type="text" value="{{ old('isbn', (string) $book->isbn) }}" placeholder="978-0-306-40615-7" required>
                        <div class="lib-hint">ISBN-10 atau ISBN-13 (tanda hubung boleh dipakai)</div>
                    </div>
                    <div>
                        <label for="published_year">Tahun terbit</label>
                        <input id="published_year" name="published_year" type="number" value="{{ old('published_year', $book->published_year) }}" placeholder="2024">
                    </div>
                </div>
                <div class="lib-grid-2">
                    <div>
                        <label for="publisher_id">Penerbit</label>
                        <select id="publisher_id" name="publisher_id">
                            <option value="">- Pilih penerbit -</option>
                            @foreach ($publishers as $publisher)
                                <option value="{{ $publisher->id }}" @selected(old('publisher_id', $book->publisher_id) == $publisher->id)>{{ $publisher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="total_stock">Total stok</label>
                        <input id="total_stock" name="total_stock" type="number" min="0" value="{{ old('total_stock', $book->total_stock ?? 1) }}" required>
                    </div>
                </div>

                <div class="lib-section-title" style="margin-top: 1.6rem;">Penulis &amp; Kategori</div>
                <div class="lib-grid-2">
                    <div>
                        <label for="author_ids">Penulis</label>
                        <select id="author_ids" name="author_ids[]" multiple size="6">
                            @foreach ($authors as $author)
                                <option value="{{ $author->id }}" @selected(in_array($author->id, old('author_ids', $book->exists ? $book->authors->pluck('id')->all() : [])))>{{ $author->name }}</option>
                            @endforeach
                        </select>
                        <div class="lib-hint">Tahan Ctrl/Cmd untuk memilih lebih dari satu</div>
                    </div>
                    <div>
                        <label for="category_ids">Kategori</label>
                        <select id="category_ids" name="category_ids[]" multiple size="6">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(in_array($category->id, old('category_ids', $book->exists ? $book->categories->pluck('id')->all() : [])))>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div class="lib-hint">Tahan Ctrl/Cmd untuk memilih lebih dari satu</div>
                    </div>
                </div>

                <div>
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="4" placeholder="Ringkasan singkat isi buku (opsional)">{{ old('description', $book->description) }}</textarea>
                </div>

                <div class="lib-form-actions">
                    <button type="submit" class="lib-btn lib-btn-primary">💾 Simpan</button>
                    <a href="{{ route('books.index') }}" class="lib-btn lib-btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
