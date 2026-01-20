<?php

namespace App\Services\User;

use App\Http\Requests\User\ContactRequest;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;

class ContactService
{
   /**
    * 問い合わせを保存するメソッド。
    * @param ContactRequest $request
    * @return Contact
    */
   public static function createContact(ContactRequest $request): Contact
   {
      return Contact::create([
         'subject' => $request->subject,
         'message' => $request->message,
         'user_id' => Auth::id(),
      ]);
   }
}
