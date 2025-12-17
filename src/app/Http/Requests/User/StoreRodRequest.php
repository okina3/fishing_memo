<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRodRequest extends FormRequest
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
         'rod_name' => [
            'required',
            'string',
            'max:25',
            Rule::unique('rods', 'name')->where(function ($query) {
               return $query->where('user_id', auth()->id());
            }),
         ],
      ];
   }

   /**
    * バリデーションエラーメッセージを定義するメソッド。
    * @return string[]
    */
   public function messages(): array
   {
      return [
         'rod_name.required' => '釣り竿を入力してください。',
         'rod_name.string' => '釣り竿名は文字列で入力してください。',
         'rod_name.max' => '釣り竿は、25文字以内で入力してください。',
         'rod_name.unique' => 'この釣り竿はすでに登録されています。',
      ];
   }
}
