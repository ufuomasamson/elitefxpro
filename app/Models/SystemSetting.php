<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value by key.
     */
    public static function set($key, $value, $type = 'string', $description = null, $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
                'group' => $group,
            ]
        );
    }

    /**
     * Cast value to appropriate type.
     */
    protected static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($value) ? (float) $value : $value;
            case 'array':
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Get all settings grouped by group.
     */
    public static function getAllGrouped()
    {
        return static::all()->groupBy('group')->map(function ($settings) {
            return $settings->pluck('value', 'key')->map(function ($value, $key) use ($settings) {
                $setting = $settings->where('key', $key)->first();
                return static::castValue($value, $setting->type);
            });
        });
    }

    /**
     * Get currency settings.
     */
    public static function getCurrencySettings()
    {
        return [
            'currency' => static::get('default_currency', 'EUR'),
            'symbol' => static::get('currency_symbol', '€'),
        ];
    }
}
