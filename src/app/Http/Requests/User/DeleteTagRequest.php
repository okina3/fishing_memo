<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class DeleteTagRequest extends FormRequest
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
            'tags' => 'required ',
        ];
    }

    /**
     * バリデーションエラーメッセージを定義するメソッド。
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'tags.required' => '削除したいタグに、チェックを入れてください。',
        ];
    }
}
