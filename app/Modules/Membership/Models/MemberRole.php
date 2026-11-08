<?php

namespace Modules\Membership\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberRole extends Model
{
    protected $fillable = ['name', 'slug', 'max_loans', 'loan_days'];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
