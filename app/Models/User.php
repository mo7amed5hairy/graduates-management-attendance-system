<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'access_token',
        'first_name',
        'father_name',
        'grandfather_name',
        'family_name',
        'name',
        'mother_name',
        'mother_father_name',
        'mother_grandfather_name',
        'email',
        'password',
        'phone',
        'image',
        'role',
        'points',
        'status',
        'address',
        'social_links',
        'national_id',
        'governorate',
        'university',
        'faculty',
        'graduation_year',
        'job_status',
        'age',
        'date_of_birth',
        'gender',
        'social_status',
        'children_count',
        'qualification_id',
        'qualification_faculty_id',
        'id_photos',
        'residence_proof',
        'graduation_attachments',
        'approval_status',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'social_links' => 'array',
            'id_photos' => 'array',
            'residence_proof' => 'array',
            'graduation_attachments' => 'array',
            'permissions' => 'array',
            'graduation_year' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function getNameAttribute($value)
    {
        if ($this->attributes['first_name'] ?? null) {
            return trim(
                ($this->attributes['first_name'] ?? '') . ' ' .
                ($this->attributes['father_name'] ?? '') . ' ' .
                ($this->attributes['grandfather_name'] ?? '') . ' ' .
                ($this->attributes['family_name'] ?? '')
            );
        }
        return $value;
    }

    public function getMotherFullNameAttribute(): string
    {
        return trim(
            ($this->attributes['mother_name'] ?? '') . ' ' .
            ($this->attributes['mother_father_name'] ?? '') . ' ' .
            ($this->attributes['mother_grandfather_name'] ?? '')
        );
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->email === 'admin@admin.com') return true;
        $perms = $this->permissions ?? [];
        return in_array($permission, $perms, true);
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function pointsTransactions(): HasMany
    {
        return $this->hasMany(PointsTransaction::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->whereNull('read_at')->latest();
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(Qualification::class);
    }

    public function qualificationFaculty(): BelongsTo
    {
        return $this->belongsTo(QualificationFaculty::class);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image ? url('files/' . $this->image) : asset('images/default-avatar.png');
    }

    public function getGovernorateNameAttribute(): string
    {
        $val = $this->governorate;
        if (!$val) return 'غير محدد';
        if (is_numeric($val)) {
            $gov = \App\Models\Governorate::find((int) $val);
            return $gov ? $gov->name : $val;
        }
        return $val;
    }
}
