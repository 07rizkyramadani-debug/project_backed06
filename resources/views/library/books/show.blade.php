<x-app-layout>
    @include('library._styles')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $book->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="lib-wrap lib-w-3xl">
            <div class="lib-card" style="color:#374151; line-height:1.9">
                <p><span class="font-semibold">ISBN:</span> {{ $book->isbn->format() }}</p>
                <p><span class="font-semibold">Penulis:</span> {{ $book->authors->pluck('name')->join(', ') }}</p>
                <p><span class="font-semibold">Penerbit:</span> {{ $book->publisher?->name ?? '-' }}</p>
                <p><span class="font-semibold">Tahun terbit:</span> {{ $book->published_year ?? '-' }}</p>
                <p><span class="font-semibold">Stok:</span> {{ $book->stock }} dari {{ $book->total_stock }}</p>
                <div>
                    <span class="font-semibold">Kategori:</span>
                    <ul class="list-disc list-inside">
                        @forelse ($book->categories as $category)
                            <li>
                                {{ $category->parent ? $category->parent->name . ' › ' : '' }}{{ $category->name }}
                                <span class="text-xs text-gray-500">(ditambahkan {{ \Illuminate\Support\Carbon::parse($category->pivot->added_at)->format('d M Y') }})</span>
                            </li>
                        @empty
                            <li>-</li>
                        @endforelse
                    </ul>
                </div>
                <p class="pt-2">{{ $book->description }}</p>
                <a href="{{ route('books.index') }}" class="lib-btn lib-btn-ghost" style="margin-top:1rem">← Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
