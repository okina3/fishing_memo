<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\User\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ユーザー用のパスワードリセットの為のメソッド。
     * @param $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Memoモデルとの一対多のリレーションを定義。
     * @return HasMany
     */
    public function memos(): HasMany
    {
        return $this->hasMany(Memo::class);
    }

    /**
     * ユーザーを、新しい順に取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableAllUsers(Builder $query): void
    {
        $query->orderBy('updated_at', 'desc');
    }

    /**
     * 選択したユーザーを取得する為のスコープ。
     * @param Builder $query
     * @param int $request_user_id
     * @return void
     */
    public function scopeAvailableSelectUser(Builder $query, int $request_user_id): void
    {
        $query->where('id', $request_user_id);
    }

    /**
     * メールアドレスから、ユーザーを特定する為のスコープ。
     * @param Builder $query
     * @param string $request_share_user
     * @return void
     */
    public function scopeAvailableSelectMailUser(Builder $query, string $request_share_user): void
    {
        $query->where('email', $request_share_user);
    }

    /**
     * 検索したメールアドレスを表示する為のスコープ。
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
            // 単語ごとに OR 条件でメールアドレスを部分一致検索
            $query->where(function (Builder $q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere('users.email', 'like', '%' . $word . '%');
                }
            });
        }
    }
}
