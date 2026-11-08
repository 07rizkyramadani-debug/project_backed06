<x-app-layout>
    @include('library._styles')
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Kategori Buku</h2></x-slot>
    <div class="py-12">
        <div class="lib-wrap lib-w-3xl">
            <div class="lib-card">
                <ul class="space-y-3 text-gray-700">
                    @foreach ($categories as $category)
                        <li>
                            <span class="font-semibold">{{ $category->name }}</span>
                            <span class="text-xs text-gray-500">({{ $category->books_count }} buku)</span>
                            @if ($category->children->isNotEmpty())
                                <ul class="ml-6 mt-1 list-disc list-inside text-sm">
                                    @foreach ($category->children as $child)
                                        <li>{{ $child->name }} <span class="text-xs text-gray-500">({{ $child->books_count }} buku)</span></li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
