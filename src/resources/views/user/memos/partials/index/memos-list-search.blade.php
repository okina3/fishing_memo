{{-- 検索フォーム --}}
<div class="mb-2">
   <div class="flex flex-col items-center">
      <p class="text-sm text-gray-500">＊釣行日、備考からメモを検索します。</p>
      <form method="get" action="{{ route('user.index') }}">
         <input class="px-2 py-1.5 mr-2 w-72 border rounded" type="text" name="keyword" placeholder="キーワードを入力">
         <button class="btn bg-blue-800 hover:bg-blue-700">
            検索
         </button>
      </form>
      {{-- エラーメッセージ（検索エリア） --}}
      <x-input-error class="mt-2 bg-white" :messages="$errors->get('keyword')" />
   </div>
</div>
