<x-app-layout>
    @include('library._styles')
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Peminjaman</h2>
            <div class="lib-actions">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('loans.trashed') }}" class="lib-btn lib-btn-ghost">Peminjaman terhapus</a>
                @endif
                <a href="{{ route('loans.create') }}" class="lib-btn lib-btn-primary">+ Peminjaman Baru</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('library._flash')
            <div class="lib-tabs">
                @php($st = request('status'))
                <a href="{{ route('loans.index') }}" class="lib-tab {{ !$st ? 'lib-tab-active' : '' }}">Semua</a>
                <a href="{{ route('loans.index', ['status' => 'borrowed']) }}" class="lib-tab {{ $st === 'borrowed' ? 'lib-tab-active' : '' }}">Dipinjam</a>
                <a href="{{ route('loans.index', ['status' => 'overdue']) }}" class="lib-tab {{ $st === 'overdue' ? 'lib-tab-active' : '' }}">Terlambat</a>
                <a href="{{ route('loans.index', ['status' => 'returned']) }}" class="lib-tab {{ $st === 'returned' ? 'lib-tab-active' : '' }}">Dikembalikan</a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="lib-table">
                        <thead><tr>
                            <th class="py-3 px-4 text-left">No. Pinjam</th><th class="py-3 px-4 text-left">Anggota</th>
                            <th class="py-3 px-4 text-left">Buku</th><th class="py-3 px-4 text-left">Jatuh tempo</th>
                            <th class="py-3 px-4 text-center">Status</th><th class="py-3 px-4 text-right">Denda</th><th class="py-3 px-4 text-center">Aksi</th>
                        </tr></thead>
                        <tbody class="text-gray-600">
                            @forelse ($loans as $loan)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4 lib-nowrap"><a class="lib-link" href="{{ route('loans.show', $loan) }}">{{ $loan->loan_number }}</a></td>
                                    <td class="py-3 px-4">{{ $loan->member_name }}<div class="text-xs text-gray-400">{{ $loan->member_number }}</div></td>
                                    <td class="py-3 px-4">{{ $loan->details->pluck('book_title')->join(', ') }}</td>
                                    <td class="py-3 px-4">{{ $loan->due_at->format('d M Y') }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($loan->isReturned()) <span class="lib-badge lib-badge-green">✓ Dikembalikan</span>
                                        @elseif ($loan->isOverdue()) <span class="lib-badge lib-badge-red">● Terlambat</span>
                                        @else <span class="lib-badge lib-badge-blue">● Dipinjam</span> @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">Rp {{ number_format($loan->total_fine, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-center lib-col-actions">
                                        <div class="lib-actions">
                                            @unless ($loan->isReturned())
                                                <form action="{{ route('loans.return', $loan) }}" method="POST" onsubmit="return confirm('Proses pengembalian?');">@csrf
                                                    <button type="submit" class="lib-btn lib-btn-success">↩ Kembalikan</button>
                                                </form>
                                            @endunless
                                            @if (auth()->user()->isAdmin())
                                                <form action="{{ route('loans.destroy', $loan) }}" method="POST" onsubmit="return confirm('Hapus peminjaman ini (soft delete)?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="lib-btn lib-btn-danger">✕ Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-4 text-center text-gray-500">Belum ada peminjaman.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $loans->links('library.pagination') }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
