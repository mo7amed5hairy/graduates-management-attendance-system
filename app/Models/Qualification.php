<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Qualification extends Model
{
    protected $fillable = ['name'];

    public function faculties(): HasMany
    {
        return $this->hasMany(QualificationFaculty::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
