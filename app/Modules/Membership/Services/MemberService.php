<?php

namespace Modules\Membership\Services;

use Modules\Membership\Models\Member;
use Modules\Shared\Contracts\MemberServiceInterface;

class MemberService implements MemberServiceInterface
{
    public function getMemberForLoan(int $memberId): ?array
    {
        $member = Member::with('role')->find($memberId);

        if (!$member) {
            return null;
        }

        return [
            'id' => $member->id,
            'member_number' => $member->member_number,
            'name' => $member->name,
            'status' => $member->status,
            'active_loans' => $member->active_loans_count,
            'max_loans' => $member->role->max_loans,
            'loan_days' => $member->role->loan_days,
        ];
    }

    public function getBorrowBlockReason(int $memberId, int $quantity): ?string
    {
        $member = Member::with(['role', 'currentMembership'])->find($memberId);

        if (!$member) {
            return 'Anggota tidak ditemukan.';
        }

        if ($member->status === Member::STATUS_SUSPENDED) {
            return "Anggota {$member->name} sedang ditangguhkan (suspended).";
        }

        if (!$member->currentMembership || !$member->currentMembership->isValid()) {
            return "Keanggotaan {$member->name} tidak aktif atau sudah berakhir.";
        }

        $max = $member->role->max_loans;
        if ($member->active_loans_count + $quantity > $max) {
            return "Batas pinjaman anggota {$member->name} terlampaui "
                . "(sedang meminjam {$member->active_loans_count}, maksimal {$max}).";
        }

        return null;
    }

    public function markBorrowing(int $memberId, int $quantity): bool
    {
        $member = Member::lockForUpdate()->find($memberId);

        if (!$member) {
            return false;
        }

        $member->increment('active_loans_count', $quantity);
        $member->update(['status' => Member::STATUS_BORROWING]);

        return true;
    }

    public function markReturned(int $memberId, int $quantity): bool
    {
        $member = Member::withTrashed()->lockForUpdate()->find($memberId);

        if (!$member) {
            return false;
        }

        $remaining = max(0, $member->active_loans_count - $quantity);
        $member->active_loans_count = $remaining;

        if ($member->status === Member::STATUS_BORROWING && $remaining === 0) {
            $member->status = Member::STATUS_ACTIVE;
        }

        $member->save();

        return true;
    }

    public function listActiveMembers(): array
    {
        return Member::active()
            ->orderBy('name')
            ->get(['id', 'member_number', 'name'])
            ->map(fn (Member $m) => [
                'id' => $m->id,
                'member_number' => $m->member_number,
                'name' => $m->name,
            ])
            ->all();
    }
}
