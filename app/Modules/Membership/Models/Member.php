<?php

namespace Modules\Membership\Models;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_BORROWING = 'borrowing';
    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'member_role_id', 'user_id', 'member_number', 'name', 'email',
        'phone', 'status', 'active_loans_count', 'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'active_loans_count' => 'integer',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(MemberRole::class, 'member_role_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /** Keanggotaan aktif terbaru */
    public function currentMembership(): HasOne
    {
        return $this->hasOne(Membership::class)->where('status'
        , 'active')->latest('ends_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_SUSPENDED);
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('member_number', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%");
        });
    }

    protected static function newFactory(): MemberFactory
    {
        return MemberFactory::new();
    }
}

