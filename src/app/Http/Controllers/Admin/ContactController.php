<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeleteContactRequest;
use App\Http\Requests\Admin\SearchKeywordRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
   /**
    * ユーザーの問い合わせ一覧を表示するメソッド。
    * @param SearchKeywordRequest $request
    * @return View
    */
   public function index(SearchKeywordRequest $request): View
   {
      // 全ての問い合わせ情報を取得する
      $perPage = 15;
      $all_contact = Contact::with(['user' => function ($q) {
         $q->withTrashed();
      }])
         ->searchKeyword($request->keyword)
         ->availableAllContacts()
         ->paginate($perPage)
         ->withQueryString();

      return view('admin.contacts.index', compact('all_contact'));
   }

   /**
    * ユーザーの問い合わせの詳細を表示するメソッド。
    * @param int $id
    * @return View
    */
   public function show(int $id): View
   {
      // 選択した問い合わせ情報を取得する
      $select_contact = Contact::with(['user' => function ($q) {
         $q->withTrashed();
      }])
         ->availableSelectContact($id)->first();

      return view('admin.contacts.show', compact('select_contact'));
   }

   /**
    * ユーザーの問い合わせを削除（ソフトデリート）するメソッド。
    * @param DeleteContactRequest $request
    * @return RedirectResponse
    */
   public function destroy(DeleteContactRequest $request): RedirectResponse
   {
      // 選択した問い合わせ情報を削除する
      Contact::availableSelectContact($request->contentId)->delete();

      return to_route('admin.contact.index')->with(['message' => 'ユーザーの問い合わせをゴミ箱に移動しました。', 'status' => 'success']);
   }
}
