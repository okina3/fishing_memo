<?php

namespace App\Services\Admin;

use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TrashedContactService
{
   /**
    * ソフトデリートした問い合わせ一覧を表示するメソッド。
    * @param string|null $keyword
    * @param int $perPage
    * @return LengthAwarePaginator
    */
   public static function allTrashedContacts(?string $keyword, int $perPage = 15): LengthAwarePaginator
   {
      return Contact::onlyTrashed()
         ->searchKeyword($keyword)
         ->availableAllContacts()
         ->paginate($perPage)
         ->withQueryString();
   }
}
