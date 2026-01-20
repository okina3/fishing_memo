<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSharedMemoRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        // users ガードで認証されていることを確認する
        return $this->user('users') !== null;
    }

    /**
     * リクエストに対するバリデーションルールを定義するメソッド。
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'memoId' => 'required|integer|exists:memos,id',
            'content' => 'required|string|max:1000',
        ];
    }

    /**
     * バリデーションエラーメッセージを定義するメソッド。
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'memoId.required' => 'メモIDが指定されていません。',
            'memoId.integer' => 'メモIDの形式が不正です。',
            'memoId.exists' => '選択されたメモは存在しません。',

            'content.required' => '備考を入力してください。',
            'content.string' => '備考は文字列で入力してください。',
            'content.max' => '備考は1000文字以内で入力してください。',
        ];
    }
}
