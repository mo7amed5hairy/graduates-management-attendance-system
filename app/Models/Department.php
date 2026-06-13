<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    protected $fillable = ['name', 'institution_id'];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
