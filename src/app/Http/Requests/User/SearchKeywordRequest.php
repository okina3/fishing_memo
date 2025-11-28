<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class SearchKeywordRequest extends FormRequest
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
    * @return array<string, mixed>
    */
   public function rules(): array
   {
      return [
         'keyword' => ['nullable', 'string', 'max:50'],
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
         'keyword.max' => 'キーワードは、50文字以内で入力してください。',
      ];
   }
}
