<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeleteContactRequest extends FormRequest
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
         'contentId' => ['required', 'integer', 'exists:contacts,id'],
      ];
   }
}
