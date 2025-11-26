<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Memo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'spot_id',
        'fishing_date',
        'start_time',
        'end_time',
        'weather',
        'air_temp',
        'max_wind',
        'wind_dir',
        'river_flow',
        'turbidity',
        'debris',
        'water_level',
        'water_temp',
        'content',
    ];

    protected $casts = [
        'fishing_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'air_temp' => 'integer',
        'max_wind' => 'integer',
        'water_level' => 'float',
        'water_temp' => 'integer',
    ];

    /**
     * Spotモデルへのリレーションを返す（一対多）。
     * @return BelongsTo
     */
    public function spot(): BelongsTo
    {
        return $this->belongsTo(Spot::class);
    }

    /**
     * Baitモデルとの多対多のリレーションを定義。
     * @return BelongsToMany
     */
    public function baits(): BelongsToMany
    {
        return $this->belongsToMany(Bait::class, 'memo_baits');
    }

    /**
     * FishNameモデルとの多対多のリレーションを定義。
     * @return BelongsToMany
     */
    public function fish_names(): BelongsToMany
    {
        return $this->belongsToMany(FishName::class, 'memo_fish_names')
            ->withPivot(['count', 'length'])
            ->withTimestamps();
    }

    /**
     * Tagモデルとの多対多のリレーションを定義。
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'memo_tags');
    }

    /**
     * Imageモデルとの多対多のリレーションを定義。
     * @return BelongsToMany
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'memo_images');
    }

    /**
     * ShareSettingモデルとの一対多のリレーションを定義。
     * @return HasMany
     */
    public function shareSettings(): HasMany
    {
        return $this->hasMany(ShareSetting::class);
    }

    /**
     * Userモデルへのリレーションを返す（一対多）。
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
