<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalFaq extends Model
{
    protected $fillable = ['question', 'answer', 'sort_order'];
}
