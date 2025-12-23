<?php

namespace App\Services\Admin;

use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactService
{
   /**
    * ユーザーからの問い合わせ一覧を表示するメソッド。
    * @param string|null $keyword
    * @param int $perPage
    * @return LengthAwarePaginator
    */
   public static function allContacts(?string $keyword, int $perPage = 15): LengthAwarePaginator
   {
      return Contact::with(['user' => function ($q) {
         $q->withTrashed();
      }])
         ->searchKeyword($keyword)
         ->availableAllContacts()
         ->paginate($perPage)
         ->withQueryString();
   }
}
