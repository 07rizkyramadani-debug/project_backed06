<x-app-layout>
    @include('library._styles')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buku Terhapus (Soft Delete)</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('library._flash')
            <div class="lib-card" style="overflow-x:auto">
                <table class="lib-table">
                    <thead><tr>
                        <th class="py-3 px-4 text-left">Judul</th><th class="py-3 px-4 text-left">ISBN</th>
                        <th class="py-3 px-4 text-left">Penulis</th><th class="py-3 px-4 text-left">Dihapus pada</th><th class="py-3 px-4 text-center">Aksi</th>
                    </tr></thead>
                    <tbody class="text-gray-600">
                        @forelse ($books as $book)
                            <tr class="border-b">
                                <td class="py-3 px-4">{{ $book->title }}</td>
                                <td class="py-3 px-4 lib-nowrap">{{ $book->isbn->format() }}</td>
                                <td class="py-3 px-4">{{ $book->authors->pluck('name')->join(', ') }}</td>
                                <td class="py-3 px-4">{{ $book->deleted_at->format('d M Y H:i') }}</td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('books.restore', $book->id) }}" method="POST">@csrf
                                        <button type="submit" class="lib-btn lib-btn-success">↺ Restore</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-500">Tidak ada buku yang dihapus.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $books->links('library.pagination') }}</div>
                <a href="{{ route('books.index') }}" class="lib-btn lib-btn-ghost" style="margin-top:1rem">← Kembali ke daftar buku</a>
            </div>
        </div>
    </div>
</x-app-layout>
