<?php

namespace Modules\Membership\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Membership\Models\Member;
use Modules\Membership\Models\MemberRole;
use Modules\Membership\Requests\StoreMemberRequest;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        // Eager loading: role dan keanggotaan aktif dimuat sekaligus
        $members = Member::with(['role', 'currentMembership'])
            ->when($request->filled('q'), fn ($q) => $q->search($request->string('q')->toString()))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('library.members.index', compact('members'));
    }

    public function create()
    {
        return view('library.members.form', [
            'member' => new Member(),
            'roles' => MemberRole::orderBy('name')->get(),
        ]);
    }

    public function store(StoreMemberRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $member = Member::create([
                'member_role_id' => $data['member_role_id'],
                'member_number' => 'AGT-' . now()->format('ym') . '-' . strtoupper(substr(uniqid(), -5)),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'status' => 'active',
                'joined_at' => now(),
            ]);

            $member->memberships()->create([
                'type' => $data['membership_type'] ?? 'regular',
                'starts_at' => now()->toDateString(),
                'ends_at' => now()->addMonths((int) ($data['membership_months'] ?? 12))->toDateString(),
                'status' => 'active',
            ]);
        });

        return redirect()->route('members.index')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(Member $member)
    {
        return view('library.members.form', [
            'member' => $member,
            'roles' => MemberRole::orderBy('name')->get(),
        ]);
    }

    public function update(StoreMemberRequest $request, Member $member)
    {
        $data = $request->validated();

        $member->update([
            'member_role_id' => $data['member_role_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'] ?? $member->status,
        ]);

        return redirect()->route('members.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    /** Soft delete */
    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('members.index')->with('success', 'Anggota dipindahkan ke daftar terhapus (soft delete).');
    }

    public function trashed()
    {
        $members = Member::onlyTrashed()->with('role')->latest('deleted_at')->paginate(10);

        return view('library.members.trashed', compact('members'));
    }

    public function restore(int $id)
    {
        Member::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('members.trashed')->with('success', 'Anggota berhasil dipulihkan.');
    }
}
