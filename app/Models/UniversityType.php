<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniversityType extends Model
{
    protected $fillable = ['name'];

    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class);
    }
}
