<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkedInSetting extends Model
{
    protected $table    = 'linkedin_settings';
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null): mixed
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function incrementSetting(string $key): int
    {
        $current = (int) static::get($key, 0);
        $next    = $current + 1;
        static::set($key, $next);
        return $next;
    }
}