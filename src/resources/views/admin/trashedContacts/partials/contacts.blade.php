{{-- ユーザーからの問い合わせ一覧の表示エリア --}}
<section class="text-gray-600 border border-gray-500 rounded-lg overflow-hidden">
   {{-- タイトル --}}
   <h1 class="heading heading_bg !bg-rose-900">削除済み問い合わせ一覧</h1>
   {{-- ユーザーからの問い合わせ一覧 --}}
   <div class="p-2">
      @foreach ($all_trashed_contacts as $contact)
         <div class="mb-3 p-2 md:flex justify-between items-center border border-gray-500 rounded-lg  bg-white">
            <div class="md:w-[70%] mr-5 font-semibold">
               {{-- 件名 --}}
               <p class="mb-1 truncate">
                  件名<span class="font-normal">・・・・・・・・</span>{{ $contact->subject }}
               </p>
               {{-- 問い合わせ内容 --}}
               <p class="mb-1 truncate">
                  問い合わせ内容<span class="font-normal">・・・</span>{{ $contact->message }}
               </p>
            </div>
            {{-- ボタンエリア --}}
            <div class="mt-2 md:w-[30%] flex md:justify-end">
               {{-- 元に戻すボタン --}}
               <form action="{{ route('admin.trashed-contact.undo') }}" method="post" class="mr-3">
                  @csrf
                  @method('patch')
                  {{-- 選択されている問い合わせのidを取得 --}}
                  <input type="hidden" name="contentId" value="{{ $contact->id }}">
                  <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">元に戻す</button>
               </form>
               {{-- 完全削除ボタン --}}
               <form onsubmit="return deleteCheck()" action="{{ route('admin.trashed-contact.destroy') }}"
                  method="post">
                  @csrf
                  @method('delete')
                  {{-- 選択されている問い合わせのidを取得 --}}
                  <input type="hidden" name="contentId" value="{{ $contact->id }}">
                  <button class="btn bg-red-600 hover:bg-red-500" type="submit">完全削除</button>
               </form>
            </div>
         </div>
      @endforeach
   </div>
</section>
