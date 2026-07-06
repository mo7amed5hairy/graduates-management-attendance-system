<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalVideo extends Model
{
    protected $fillable = ['title', 'url', 'image', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
