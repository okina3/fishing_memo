{{-- ユーザーの検索の表示エリア --}}
<section class="mb-3 px-3 py-1 text-slate-100 border border-gray-500 rounded-lg bg-rose-900">
   <form action="{{ route($routeName) }}" method="get">
      <div class="sm:flex items-center">
         <div class="heading">メールアドレスから検索</div>
         <div class="hidden sm:block">・・・</div>
         {{-- メールアドレスを入力 --}}
         <input class="py-2 w-60 text-slate-900 border border-gray-500 rounded-lg" name="keyword" placeholder="メールアドレスを入力">
         {{-- 検索するボタン --}}
         <button class="ml-2 btn btn-bk bg-yellow-500 hover:bg-yellow-400">検索する</button>
      </div>
   </form>
   {{-- エラーメッセージ（検索エリア） --}}
   <x-input-error class="mt-2 bg-white" :messages="$errors->get('keyword')" />
</section>
