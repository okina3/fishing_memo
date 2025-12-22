{{-- ユーザーからの問い合わせ一覧の表示エリア --}}
<section class="text-gray-600 border border-gray-500 rounded-lg overflow-hidden">
   {{-- タイトル --}}
   <h1 class="heading heading_bg bg-rose-900">ユーザーからの問い合わせ一覧</h1>
   {{-- ユーザーからの問い合わせ一覧 --}}
   <div class="p-2">
      @foreach ($all_contact as $contact)
         <div class="mb-5 p-2 md:flex justify-between items-center border border-gray-500 rounded-lg bg-white">
            <div class="md:w-[88%] mr-5 font-semibold">
               {{-- ユーザー名 --}}
               <p class="mb-1 truncate">
                  ユーザー名<span class="font-normal">・・・・・</span>
                  <span class="border-b border-slate-400">
                     {{ optional($contact->user)->name ?? '' }}
                  </span>
               </p>
               {{-- 件名 --}}
               <p class="mb-1 truncate">
                  件名<span class="font-normal">・・・・・・・・</span>{{ $contact->subject }}
               </p>
               {{-- 問い合わせ内容 --}}
               <p class="mb-1 truncate">
                  問い合わせ内容<span class="font-normal">・・・</span>{{ $contact->message }}
               </p>
            </div>
            {{-- 詳細ボタン --}}
            <div class="md:w-[12%] flex justify-end">
               <button class="btn bg-sky-900 hover:bg-sky-700"
                  onclick="location.href='{{ route('admin.contact.show', ['contact' => $contact->id]) }}'">
                  詳細
               </button>
            </div>
         </div>
      @endforeach
   </div>
</section>
