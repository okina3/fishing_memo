<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class SessionService
{
    /**
     * ブラウザバック用のセッションに値を設定するメソッド。
     * @return void
     * @throws Exception
     */
    public static function setBrowserBackSession(): void
    {
        // 環境変数 BROWSER_BACK_KEY の値を取得
        $back_key = config('common_browser_back.browser_back_key');

        // BROWSER_BACK_KEY が null または空の場合は例外をスロー
        if (empty($back_key)) {
            throw new Exception('BROWSER_BACK_KEY is not set in the environment file.');
        }

        // 暗号化してセッションに保存
        session()->flash('back_button_clicked', encrypt($back_key));
    }

    /**
     * ブラウザバック用のセッションに値がない場合、また、値が一致しない場合のリダイレクトするメソッド。
     * @return void
     */
    public static function clickBrowserBackSession(): void
    {
        try {
            $has = session()->has('back_button_clicked');
            $value = $has ? decrypt(session('back_button_clicked')) : null;

            if (!$has || $value !== config('common_browser_back.browser_back_key')) {
                throw new HttpResponseException(
                    redirect()->route('user.index')
                        ->with(['message' => '予期せぬエラーが起きました。トップページに戻ります。', 'status' => 'error'])
                );
            }
        } catch (\Throwable $e) {
            // 復号失敗など異常時も安全にトップへリダイレクト
            throw new HttpResponseException(
                redirect()->route('user.index')
                    ->with(['message' => '予期せぬエラーが起きました。トップページに戻ります。', 'status' => 'error'])
            );
        }
    }

    /**
     * ブラウザバック用のセッションの値を削除するメソッド。
     * @return void
     */
    public static function resetBrowserBackSession(): void
    {
        session()->forget('back_button_clicked');
    }
}
