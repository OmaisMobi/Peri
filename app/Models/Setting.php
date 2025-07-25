<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['type', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Retrieve or initialize settings by type.
     */
    public static function getByType(string $type): array
    {
        $setting = self::firstOrCreate(['type' => $type], ['value' => []]);
        return $setting->value ?? [];
    }

    /**
     * Update settings by type.
     */
    public static function setByType(string $type, array $value): void
    {
        self::updateOrCreate(
            ['type' => $type],
            ['value' => $value]
        );
    }
}
