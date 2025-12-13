<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Memo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'fishing_date',
        'start_time',
        'end_time',
        'weather',
        'air_temp',
        'wind_dir',
        'content',
    ];

    protected $casts = [
        'fishing_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'air_temp' => 'integer',
    ];

    /**
     * Spotモデルとの多対多のリレーションを定義。
     * @return BelongsToMany
     */
    public function spots(): BelongsToMany
    {
        return $this->belongsToMany(Spot::class, 'memo_spots')
            ->withPivot(['river_flow', 'turbidity', 'water_level', 'water_temp']);
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
            ->withPivot(['count', 'length']);
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

    /**
     * 自分自身の、全てのメモを取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableAllMemos(Builder $query): void
    {
        $query->with('shareSettings')
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
    }

    /**
     * 自分自身の、選択したメモを取得する為のスコープ。
     * @param Builder $query
     * @param int $id
     * @return void
     */
    public function scopeAvailableSelectMemo(Builder $query, int $id): void
    {
        $query->where('id', $id)
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at');
    }

    /**
     * 自分自身の、全ての削除済みのメモを取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableAllTrashedMemos(Builder $query): void
    {
        $query->onlyTrashed()
            ->where('user_id', Auth::id())
            ->orderBy('deleted_at', 'desc');
    }

    /**
     * 自分自身の、選択した削除済みのメモを取得する為のスコープ。
     * @param Builder $query
     * @param int $request_memo_id
     * @return void
     */
    public function scopeAvailableSelectTrashedMemo(Builder $query, int $request_memo_id): void
    {
        $query->onlyTrashed()
            ->where('id', $request_memo_id)
            ->where('user_id', Auth::id());
    }
}
