<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualificationFaculty extends Model
{
    protected $fillable = ['qualification_id', 'name'];

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(Qualification::class);
    }
}
