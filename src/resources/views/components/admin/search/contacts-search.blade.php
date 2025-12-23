{{-- 検索の表示エリア --}}
<section class="mb-5 px-3 py-2 text-slate-100 border border-gray-500 rounded-lg bg-rose-900">
   <form action="{{ route($routeName ?? 'admin.contact.index') }}" method="get">
      <div class="sm:flex items-center">
         <div class="heading">キーワードから検索</div>
         <div class="hidden sm:block">・・・・・</div>
         {{-- キーワードを入力 --}}
         <input class="py-2 w-60 text-slate-900 border border-gray-500 rounded-lg" name="keyword" placeholder="キーワードを入力">
         {{-- 検索するボタン --}}
         <button class="ml-2 btn btn-bk bg-yellow-500 hover:bg-yellow-400">検索する</button>
      </div>
   </form>
   {{-- コメント --}}
   <p class="text-sm mt-2">※ キーワードは、件名、問い合わせ内容の、両方から検索します。</p>
   {{-- エラーメッセージ（検索エリア） --}}
   <x-input-error class="mt-2 bg-white" :messages="$errors->get('keyword')" />
</section>
