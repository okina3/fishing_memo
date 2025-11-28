<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class IndexUserRequest extends FormRequest
{
   /**
    * @return bool
    */
   public function authorize(): bool
   {
      // admin ガードで認証されていることを確認する
      return $this->user('admin') !== null;
   }

   /**
    * リクエストに対するバリデーションルールを定義するメソッド。
    * @return array<string, mixed>
    */
   public function rules(): array
   {
      return [
         'keyword' => ['nullable', 'string', 'max:100'],
      ];
   }

   /**
    * バリデーションエラーメッセージを定義するメソッド。
    * @return string[]
    */
   public function messages(): array
   {
      return [
         'keyword.string' => 'キーワードは、文字列で指定してください。',
         'keyword.max' => 'キーワードは、100文字以内で入力してください。',
      ];
   }
}
