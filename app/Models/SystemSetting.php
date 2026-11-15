<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    /**
     * Ambil nilai pengaturan berdasarkan key.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember("system_setting:{$key}", 300, function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (!$setting || $setting->value === null) {
            return $default;
        }

        if ($setting->is_encrypted) {
            try {
                return Crypt::decryptString($setting->value);
            } catch (\Throwable $e) {
                return $default;
            }
        }

        return $setting->value;
    }

    /**
     * Simpan atau perbarui nilai pengaturan.
     */
    public static function setValue(string $key, mixed $value, bool $encrypt = false): self
    {
        $storedValue = ($value !== null && $encrypt)
            ? Crypt::encryptString((string) $value)
            : ($value !== null ? (string) $value : null);

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storedValue,
                'is_encrypted' => $encrypt,
            ]
        );

        Cache::forget("system_setting:{$key}");

        return $setting;
    }

    /**
     * Hapus key pengaturan.
     */
    public static function removeKey(string $key): void
    {
        static::where('key', $key)->delete();
        Cache::forget("system_setting:{$key}");
    }
}
