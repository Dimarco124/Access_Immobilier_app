<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::remember("platform_setting_{$key}", 60, function () use ($key, $default) {
                $row = static::where('key', $key)->first();
                return $row ? $row->value : $default;
            });
        } catch (\Throwable $e) {
            return $default;
        }
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("platform_setting_{$key}");
    }
}
