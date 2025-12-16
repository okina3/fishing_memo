<x-app-layout>
   <div class="px-2 py-2 bg-slate-200">
      {{-- フラッシュメッセージ --}}
      <x-common.flash-message status="session('status')" />
      <div class="mb-2 md:flex justify-between">
         {{-- メモ一覧の表示エリア --}}
         @include('user.memos.partials.index.memos-list')
      </div>
   </div>
   <script>
      'use strict'

      // 削除のアラート
      function deleteCheck() {
         const RESULT = confirm('共有設定も解除されますが、本当に削除してもいいですか?');
         if (!RESULT) alert("削除をキャンセルしました");
         return RESULT;
      }
   </script>
</x-app-layout>
