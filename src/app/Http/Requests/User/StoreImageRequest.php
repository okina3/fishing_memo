<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
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
            'images' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    /**
     * バリデーションエラーメッセージを定義するメソッド。
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'images.required' => '画像が指定されていません。',
            'images.image' => '指定されたファイルが画像ではありません。',
            'images.mimes' => '指定された拡張子(jpg/jpeg/png)ではありません。',
            'images.max' => 'ファイルサイズは2MB以内にしてください。',
        ];
    }
}
