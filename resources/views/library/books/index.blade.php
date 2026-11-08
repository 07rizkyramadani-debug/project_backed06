<x-app-layout>
    @include('library._styles')
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Buku</h2>
            @if (auth()->user()->isAdmin())
                <div class="lib-actions">
                    <a href="{{ route('books.trashed') }}" class="lib-btn lib-btn-ghost">Buku terhapus</a>
                    <a href="{{ route('books.create') }}" class="lib-btn lib-btn-primary">+ Tambah Buku</a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('library._flash')

            <form method="GET" class="lib-card lib-filter">
                <div>
                    <label class="lib-lbl">Cari (judul / ISBN / penulis)</label>
                    <input type="text" name="q" value="{{ request('q') }}" >
                </div>
                <div>
                    <label class="lib-lbl">Kategori</label>
                    <select name="category" >
                        <option value="">Semua</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="lib-check">
                    <input type="checkbox" name="available" value="1" @checked(request()->boolean('available')) class="rounded border-gray-300 mr-2">
                    Hanya yang tersedia
                </label>
                <button type="submit" class="lib-btn lib-btn-primary">🔍 Filter</button>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="lib-table">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 text-left">Judul</th>
                                <th class="py-3 px-4 text-left">ISBN</th>
                                <th class="py-3 px-4 text-left">Penulis</th>
                                <th class="py-3 px-4 text-left">Penerbit</th>
                                <th class="py-3 px-4 text-left">Kategori</th>
                                <th class="py-3 px-4 text-center">Stok</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @forelse ($books as $book)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-4"><a class="lib-link" href="{{ route('books.show', $book) }}">{{ $book->title }}</a></td>
                                    <td class="py-3 px-4 lib-nowrap">{{ $book->isbn->format() }}</td>
                                    <td class="py-3 px-4">{{ $book->authors->pluck('name')->join(', ') }}</td>
                                    <td class="py-3 px-4">{{ $book->publisher?->name ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ $book->categories->pluck('name')->join(', ') }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="lib-badge {{ $book->stock === 0 ? 'lib-badge-red' : ($book->stock <= 2 ? 'lib-badge-amber' : 'lib-badge-green') }}">{{ $book->stock }} / {{ $book->total_stock }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-center lib-col-actions">
                                        @if (auth()->user()->isAdmin())
                                            <div class="lib-actions">
                                                <a href="{{ route('books.edit', $book) }}" class="lib-btn lib-btn-edit">✎ Edit</a>
                                                <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Hapus buku ini (soft delete)?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="lib-btn lib-btn-danger">✕ Hapus</button>
                                                </form>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-4 text-center text-gray-500">Belum ada data buku.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $books->links('library.pagination') }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
