<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'location',
        'event_date',
        'points',
        'created_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(EventAttendance::class)->whereNotNull('attended_at');
    }

    public function getAttendanceCountAttribute(): int
    {
        return $this->attendees()->count();
    }

    public function getRegisteredCountAttribute(): int
    {
        return $this->attendances()->count();
    }
}
