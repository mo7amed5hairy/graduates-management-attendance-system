<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, string $default = ''): string
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}
