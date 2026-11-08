<x-app-layout>
    @include('library._styles')
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Peminjaman Baru</h2></x-slot>
    <div style="padding: 2rem 0;">
        <div class="lib-wrap lib-w-3xl">
            @include('library._flash')
            <form method="POST" action="{{ route('loans.store') }}" class="lib-card lib-form">
                @csrf

                <div class="lib-section-title">Anggota</div>
                <div>
                    <label for="member_id">Peminjam</label>
                    <select id="member_id" name="member_id" required>
                        <option value="">- Pilih anggota -</option>
                        @foreach ($members as $member)
                            <option value="{{ $member['id'] }}" @selected(old('member_id') == $member['id'])>{{ $member['member_number'] }} - {{ $member['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lib-section-title" style="margin-top: 1.6rem;">Buku yang dipinjam</div>
                <div>
                    @for ($i = 0; $i < 3; $i++)
                        <div class="lib-item-row">
                            <select name="items[{{ $i }}][book_id]" aria-label="Buku {{ $i + 1 }}" @if ($i === 0) required @endif>
                                <option value="">{{ $i === 0 ? '- Pilih buku -' : '- Buku tambahan (opsional) -' }}</option>
                                @foreach ($books as $book)
                                    <option value="{{ $book['id'] }}">{{ $book['title'] }} — {{ $book['isbn'] }} (stok {{ $book['stock'] }})</option>
                                @endforeach
                            </select>
                            <input type="number" name="items[{{ $i }}][quantity]" value="1" min="1" max="5" aria-label="Jumlah buku {{ $i + 1 }}" title="Jumlah">
                        </div>
                    @endfor
                    <div class="lib-hint">Isi minimal satu buku. Kolom di sebelah kanan adalah jumlah eksemplar.</div>
                </div>

                <div class="lib-section-title" style="margin-top: 1.6rem;">Detail Peminjaman</div>
                <div>
                    <label for="loan_days">Lama pinjam (hari)</label>
                    <input id="loan_days" name="loan_days" type="number" min="1" max="30" value="{{ old('loan_days') }}" placeholder="Kosongkan untuk mengikuti role anggota">
                </div>
                <div>
                    <label for="notes">Catatan</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Catatan peminjaman (opsional)">{{ old('notes') }}</textarea>
                </div>

                <div class="lib-form-actions">
                    <button type="submit" class="lib-btn lib-btn-primary">📚 Proses Peminjaman</button>
                    <a href="{{ route('loans.index') }}" class="lib-btn lib-btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
