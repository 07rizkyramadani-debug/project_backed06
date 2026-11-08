<x-app-layout>
    @include('library._styles')
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Penulis</h2></x-slot>
    <div class="py-12">
        <div class="lib-wrap lib-w-5xl">
            <div class="lib-card" style="overflow-x:auto">
                <table class="lib-table">
                    <thead><tr>
                        <th class="py-3 px-4 text-left">Nama</th><th class="py-3 px-4 text-center">Jumlah buku</th><th class="py-3 px-4 text-left">Judul buku</th>
                    </tr></thead>
                    <tbody class="text-gray-600">
                        @foreach ($authors as $author)
                            <tr class="border-b">
                                <td class="py-3 px-4">{{ $author->name }}</td>
                                <td class="py-3 px-4 text-center">{{ $author->books_count }}</td>
                                <td class="py-3 px-4">{{ $author->books->pluck('title')->join(', ') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $authors->links('library.pagination') }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
