<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject',
        'message',
    ];

    /**
     * Userモデルへのリレーションを返す（一対多）。
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 全てのユーザーの問い合わせを取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableAllContacts(Builder $query): void
    {
        $query->orderBy('created_at', 'desc');
    }

    /**
     * 選択したユーザーの問い合わせを取得する為のスコープ。
     * @param Builder $query
     * @param int $id
     * @return void
     */
    public function scopeAvailableSelectContact(Builder $query, int $id): void
    {
        $query->where('id', $id);
    }

    /**
     * 検索した件名、問い合わせを表示するの為のスコープ。
     * @param Builder $query
     * @param string|null $keyword
     * @return void
     */
    public function scopeSearchKeyword(Builder $query, ?string $keyword = null): void
    {
        // キーワードが指定されていれば検索をする
        if ($keyword !== null && $keyword !== '') {
            // 全角スペースを半角に変換
            $spaceConvert = mb_convert_kana($keyword, 's');
            // 空白で分割して単語配列にする
            $keywords = preg_split('/\s+/', $spaceConvert, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            // 単語ごとに OR 条件で 件名/問い合わせ内容 を部分一致検索
            $query->where(function (Builder $q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere(function (Builder $qq) use ($word) {
                        $qq->where('contacts.subject', 'like', '%' . $word . '%')
                            ->orWhere('contacts.message', 'like', '%' . $word . '%');
                    });
                }
            });
        }
    }
}
