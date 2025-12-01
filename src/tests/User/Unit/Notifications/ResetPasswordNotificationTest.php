<?php

namespace Tests\User\Unit\Notifications;

use App\Notifications\User\ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use ReflectionClass;
use stdClass;
use Tests\User\TestCase;

class ResetPasswordNotificationTest extends TestCase
{
    // メールメッセージの内容をテスト。
    public function testToMail()
    {
        // パスワードリセット通知用のトークンを設定
        $token = 'sample_token';
        // リセットパスワード通知インスタンスを作成
        $notification = new ResetPasswordNotification($token);

        // メールメッセージを生成。通知対象のオブジェクトを作成
        $notifiable = new stdClass();
        $notifiable->email = 'user@example.com';
        // メールメッセージを生成するメソッドを呼び出し
        $mailMessage = $notification->toMail($notifiable);

        // 期待されるパスワードリセットURLを作成
        $url = route('password.reset', [
            'token' => $token,
            'email' => $notifiable->email,
        ]);
        // メールメッセージが MailMessage クラスのインスタンスであることを確認
        $this->assertInstanceOf(MailMessage::class, $mailMessage);

        // メールメッセージの内容を反射クラスを使用して検証
        $reflectionClass = new ReflectionClass($mailMessage);
        // メールの件名を取得するためのプロパティを取得
        $subject = $reflectionClass->getProperty('subject');
        // メールの挨拶文を取得するためのプロパティを取得
        $greeting = $reflectionClass->getProperty('greeting');
        // メールのアクションテキストを取得するためのプロパティを取得
        $actionText = $reflectionClass->getProperty('actionText');
        // メールのアクションURLを取得するためのプロパティを取得
        $actionUrl = $reflectionClass->getProperty('actionUrl');

        // 件名が期待通りの値であることを確認
        $this->assertEquals('ユーザー様 パスワードリセットURLの送付', $subject->getValue($mailMessage));
        // 挨拶文が期待通りの値であることを確認
        $this->assertEquals('いつもご利用いただきありがとうございます', $greeting->getValue($mailMessage));
        // アクションテキストが期待通りの値であることを確認
        $this->assertEquals('パスワードをリセット', $actionText->getValue($mailMessage));
        // アクションURLが期待通りの値であることを確認
        $this->assertEquals($url, $actionUrl->getValue($mailMessage));
    }


    // getEmailForPasswordReset() メソッドを優先して使用することを確認するテスト
    public function testToMailPrefersGetEmailForPasswordReset()
    {
        // パスワードリセット通知用のトークンを設定
        $token = 'token_with_method_pref';
        // リセットパスワード通知インスタンスを作成
        $notification = new ResetPasswordNotification($token);

        // getEmailForPasswordReset() を持つ Notifiable を用意
        $notifiable = new class {
            public string $email = 'fallback@example.com';
            public function getEmailForPasswordReset(): string
            {
                // 優先されるメールアドレス
                return 'special@example.com';
            }
        };
        // メールメッセージを生成するメソッドを呼び出し（ここで分岐）
        $mailMessage = $notification->toMail($notifiable);

        // 期待されるパスワードリセットURLを作成（special@example.com）
        $expectedUrl = route('password.reset', [
            'token' => $token,
            'email' => 'special@example.com',
        ]);

        // MailMessage の内部プロパティを取得するために Reflection を使用
        $reflectionClass = new ReflectionClass($mailMessage);
        // MailMessage ではプロパティが保護されているため、Reflection 経由でアクセス
        $actionUrl = $reflectionClass->getProperty('actionUrl');
        // アクションURLが期待通りの値であることを確認
        $this->assertEquals($expectedUrl, $actionUrl->getValue($mailMessage));
    }
}
