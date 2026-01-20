<x-app-layout>
   <div class="p-2 bg-slate-200">
      {{-- フラッシュメッセージ --}}
      <x-common.flash-message status="session('status')" />
      <div class="mb-2">
         {{-- 検索エリア --}}
         @include('user.memos.partials.index.memos-list-search')
         {{-- メモ一覧の表示エリア --}}
         @include('user.memos.partials.index.memos-list')
      </div>
      {{-- ページネーション（ページが複数あるときのみ表示） --}}
      @if (isset($all_memos) && $all_memos->hasPages())
         <div class="mt-4">
            {{ $all_memos->links() }}
         </div>
      @endif
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
