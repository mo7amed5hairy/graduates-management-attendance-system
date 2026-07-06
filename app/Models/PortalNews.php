<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalNews extends Model
{
    protected $fillable = ['title', 'url', 'image', 'news_date', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'news_date' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
