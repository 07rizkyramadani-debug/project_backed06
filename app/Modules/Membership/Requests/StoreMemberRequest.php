<?php

namespace Modules\Membership\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $member = $this->route('member');
        $ignoreId = is_object($member) ? $member->id : $member;

        return [
            'member_role_id' => ['required', 'exists:member_roles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('members', 'email')->ignore($ignoreId)],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', Rule::in(['active', 'suspended'])],
            'membership_type' => ['nullable', Rule::in(['regular', 'premium'])],
            'membership_months' => ['nullable', 'integer', 'between:1,36'],
        ];
    }
}
