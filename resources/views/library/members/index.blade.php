<x-app-layout>
    @include('library._styles')
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Anggota</h2>
            @if (auth()->user()->isAdmin())
                <div class="lib-actions">
                    <a href="{{ route('members.trashed') }}" class="lib-btn lib-btn-ghost">Anggota terhapus</a>
                    <a href="{{ route('members.create') }}" class="lib-btn lib-btn-primary">+ Tambah Anggota</a>
                </div>
            @endif
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('library._flash')
            <form method="GET" class="lib-card lib-filter">
                <div>
                    <label class="lib-lbl">Cari (nama / nomor / email)</label>
                    <input type="text" name="q" value="{{ request('q') }}" >
                </div>
                <button type="submit" class="lib-btn lib-btn-primary">🔍 Cari</button>
            </form>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="lib-table">
                        <thead><tr>
                            <th class="py-3 px-4 text-left">No. Anggota</th><th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">Role</th><th class="py-3 px-4 text-left">Keanggotaan</th>
                            <th class="py-3 px-4 text-center">Status</th><th class="py-3 px-4 text-center">Pinjaman aktif</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr></thead>
                        <tbody class="text-gray-600">
                            @forelse ($members as $member)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4">{{ $member->member_number }}</td>
                                    <td class="py-3 px-4">{{ $member->name }}<div class="text-xs text-gray-400">{{ $member->email }}</div></td>
                                    <td class="py-3 px-4">{{ $member->role->name }} <span class="text-xs text-gray-400">(maks. {{ $member->role->max_loans }})</span></td>
                                    <td class="py-3 px-4">
                                        @if ($member->currentMembership)
                                            {{ ucfirst($member->currentMembership->type) }} s.d. {{ $member->currentMembership->ends_at->format('d M Y') }}
                                        @else
                                            <span class="lib-badge lib-badge-red">Tidak aktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($member->status === 'suspended') <span class="lib-badge lib-badge-red">Ditangguhkan</span>
                                        @elseif ($member->status === 'borrowing') <span class="lib-badge lib-badge-blue">Meminjam</span>
                                        @else <span class="lib-badge lib-badge-green">Aktif</span> @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">{{ $member->active_loans_count }}</td>
                                    <td class="py-3 px-4 text-center lib-col-actions">
                                        @if (auth()->user()->isAdmin())
                                            <div class="lib-actions">
                                                <a href="{{ route('members.edit', $member) }}" class="lib-btn lib-btn-edit">✎ Edit</a>
                                                <form action="{{ route('members.destroy', $member) }}" method="POST" onsubmit="return confirm('Hapus anggota ini (soft delete)?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="lib-btn lib-btn-danger">✕ Hapus</button>
                                                </form>
                                            </div>
                                        @else - @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-4 text-center text-gray-500">Belum ada anggota.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $members->links('library.pagination') }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
