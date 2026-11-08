<?php

namespace Modules\Shared\Contracts;

/**
 * Contract Membership Context -> dipakai oleh Circulation Context.
 */
interface MemberServiceInterface
{
    /** Data anggota untuk keperluan peminjaman atau null bila tidak ditemukan. */
    public function getMemberForLoan(int $memberId): ?array;

    /** Alasan anggota tidak boleh meminjam, atau null bila boleh. */
    public function getBorrowBlockReason(int $memberId, int $quantity): ?string;

    /** Perbarui status anggota menjadi "borrowing" dan tambah jumlah pinjaman aktif. */
    public function markBorrowing(int $memberId, int $quantity): bool;

    /** Kurangi pinjaman aktif; status kembali "active" bila tidak ada pinjaman. */
    public function markReturned(int $memberId, int $quantity): bool;

    /** Daftar anggota yang boleh meminjam (untuk form peminjaman). */
    public function listActiveMembers(): array;
}

