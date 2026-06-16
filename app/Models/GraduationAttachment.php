<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraduationAttachment extends Model
{
    protected $fillable = [
        'change_request_id',
        'file_path',
        'original_name',
        'mime_type',
    ];

    public function changeRequest(): BelongsTo
    {
        return $this->belongsTo(ProfileChangeRequest::class, 'change_request_id');
    }
}
