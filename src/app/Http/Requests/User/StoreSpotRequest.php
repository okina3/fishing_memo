<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSpotRequest extends FormRequest
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
         'spot_name' => [
            'required',
            'string',
            'max:30',
            Rule::unique('spots', 'name')->where(function ($query) {
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
         'spot_name.required' => '釣り場を入力してください。',
         'spot_name.string' => '釣り場名は文字列で入力してください。',
         'spot_name.max' => '釣り場は、30文字以内で入力してください。',
         'spot_name.unique' => 'この釣り場はすでに登録されています。',
      ];
   }
}
