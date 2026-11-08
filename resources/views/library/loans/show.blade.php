<x-app-layout>
    @include('library._styles')
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Peminjaman {{ $loan->loan_number }}</h2></x-slot>
    <div class="py-12">
        <div class="lib-wrap lib-w-3xl">
            @include('library._flash')
            <div class="lib-card" style="color:#374151; line-height:1.9">
                <p><span class="font-semibold">Anggota:</span> {{ $loan->member_number }} - {{ $loan->member_name }}</p>
                <p><span class="font-semibold">Tanggal pinjam:</span> {{ $loan->borrowed_at->format('d M Y H:i') }}</p>
                <p><span class="font-semibold">Jatuh tempo:</span> {{ $loan->due_at->format('d M Y H:i') }}</p>
                <p><span class="font-semibold">Dikembalikan:</span> {{ $loan->returned_at?->format('d M Y H:i') ?? '-' }}</p>
                <p><span class="font-semibold">Total denda:</span> Rp {{ number_format($loan->total_fine, 0, ',', '.') }}</p>
                <table class="lib-table" style="margin-top:1rem">
                    <thead><tr>
                        <th class="py-2 px-3 text-left">Judul (snapshot)</th><th class="py-2 px-3 text-left">ISBN</th>
                        <th class="py-2 px-3 text-center">Jumlah</th><th class="py-2 px-3 text-right">Denda</th>
                    </tr></thead>
                    <tbody>
                        @foreach ($loan->details as $detail)
                            <tr class="border-b">
                                <td class="py-2 px-3">{{ $detail->book_title }}</td>
                                <td class="py-2 px-3">{{ $detail->book_isbn }}</td>
                                <td class="py-2 px-3 text-center">{{ $detail->quantity }}</td>
                                <td class="py-2 px-3 text-right">Rp {{ number_format($detail->fine_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pt-4 flex items-center space-x-4">
                    @unless ($loan->isReturned())
                        <form action="{{ route('loans.return', $loan) }}" method="POST">@csrf
                            <button type="submit" class="lib-btn lib-btn-success" style="padding:.5rem 1rem;font-size:.85rem">↩ Proses Pengembalian</button>
                        </form>
                    @endunless
                    <a href="{{ route('loans.index') }}" class="lib-btn lib-btn-ghost">← Kembali</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
