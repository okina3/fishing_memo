<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    // トークンプロパティを追加
    public string $token;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * 通知のメール表現をする為のメソッド。
     * @param object $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        // ユーザー用のパスワードリセットURLを生成（トークン＋メール付与）
        $email = method_exists($notifiable, 'getEmailForPasswordReset')
            ? $notifiable->getEmailForPasswordReset()
            : ($notifiable->email ?? null);

        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $email,
        ]);

        return (new MailMessage)
            ->subject('ユーザー様' . ' パスワードリセットURLの送付')
            ->greeting('いつもご利用いただきありがとうございます')
            ->line('以下のボタンから、パスワードの再設定を行ってください。')
            ->action('パスワードをリセット', $url)
            ->line('このメールに心当たりがない場合は、破棄してください。');
    }
}
