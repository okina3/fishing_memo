<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBaitRequest extends FormRequest
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
         'bait_name' => [
            'required',
            'string',
            'max:30',
            Rule::unique('baits', 'name')->where(function ($query) {
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
         'bait_name.required' => 'エサを入力してください。',
         'bait_name.string' => 'エサ名は文字列で入力してください。',
         'bait_name.max' => 'エサは、30文字以内で入力してください。',
         'bait_name.unique' => 'このエサはすでに登録されています。',
      ];
   }
}
