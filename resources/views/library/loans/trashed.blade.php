<x-app-layout>
    @include('library._styles')
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Peminjaman Terhapus (Soft Delete)</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('library._flash')
            <div class="lib-card" style="overflow-x:auto">
                <table class="lib-table">
                    <thead><tr>
                        <th class="py-3 px-4 text-left">No. Pinjam</th><th class="py-3 px-4 text-left">Anggota</th>
                        <th class="py-3 px-4 text-left">Buku</th><th class="py-3 px-4 text-left">Dihapus pada</th><th class="py-3 px-4 text-center">Aksi</th>
                    </tr></thead>
                    <tbody class="text-gray-600">
                        @forelse ($loans as $loan)
                            <tr class="border-b">
                                <td class="py-3 px-4 lib-nowrap">{{ $loan->loan_number }}</td>
                                <td class="py-3 px-4">{{ $loan->member_name }}</td>
                                <td class="py-3 px-4">{{ $loan->details->pluck('book_title')->join(', ') }}</td>
                                <td class="py-3 px-4">{{ $loan->deleted_at->format('d M Y H:i') }}</td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('loans.restore', $loan->id) }}" method="POST">@csrf
                                        <button type="submit" class="lib-btn lib-btn-success">↺ Restore</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-500">Tidak ada peminjaman yang dihapus.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $loans->links('library.pagination') }}</div>
                <a href="{{ route('loans.index') }}" class="lib-btn lib-btn-ghost" style="margin-top:1rem">← Kembali ke daftar peminjaman</a>
            </div>
        </div>
    </div>
</x-app-layout>
