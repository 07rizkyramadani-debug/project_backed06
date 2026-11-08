@php($editing = $member->exists)
<x-app-layout>
    @include('library._styles')
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $editing ? 'Edit Anggota' : 'Tambah Anggota' }}</h2></x-slot>
    <div style="padding: 2rem 0;">
        <div class="lib-wrap lib-w-2xl">
            @include('library._flash')
            <form method="POST" action="{{ $editing ? route('members.update', $member) : route('members.store') }}" class="lib-card lib-form">
                @csrf
                @if ($editing) @method('PUT') @endif

                <div class="lib-section-title">Data Anggota</div>
                <div>
                    <label for="name">Nama lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $member->name) }}" placeholder="Nama anggota" required>
                </div>
                <div class="lib-grid-2">
                    <div>
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $member->email) }}" placeholder="nama@email.com" required>
                    </div>
                    <div>
                        <label for="phone">No. telepon</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $member->phone) }}" placeholder="08xxxxxxxxxx">
                    </div>
                </div>
                <div>
                    <label for="member_role_id">Role anggota</label>
                    <select id="member_role_id" name="member_role_id" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected(old('member_role_id', $member->member_role_id) == $role->id)>{{ $role->name }} (maks. {{ $role->max_loans }} buku, {{ $role->loan_days }} hari)</option>
                        @endforeach
                    </select>
                </div>

                @if ($editing)
                    <div>
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="active" @selected($member->status !== 'suspended')>Aktif</option>
                            <option value="suspended" @selected($member->status === 'suspended')>Ditangguhkan</option>
                        </select>
                    </div>
                @else
                    <div class="lib-section-title" style="margin-top: 1.6rem;">Keanggotaan</div>
                    <div class="lib-grid-2">
                        <div>
                            <label for="membership_type">Tipe keanggotaan</label>
                            <select id="membership_type" name="membership_type">
                                <option value="regular">Regular</option>
                                <option value="premium">Premium</option>
                            </select>
                        </div>
                        <div>
                            <label for="membership_months">Masa keanggotaan (bulan)</label>
                            <input id="membership_months" name="membership_months" type="number" min="1" max="36" value="12">
                        </div>
                    </div>
                @endif

                <div class="lib-form-actions">
                    <button type="submit" class="lib-btn lib-btn-primary">💾 Simpan</button>
                    <a href="{{ route('members.index') }}" class="lib-btn lib-btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
