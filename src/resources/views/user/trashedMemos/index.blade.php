<x-app-layout>
   <div class="p-2 bg-slate-200">
      {{-- フラッシュメッセージ --}}
      <x-common.flash-message status="session('status')" />
      <div class="mb-2">
         {{-- ソフトデリートされたメモ一覧 --}}
         @include('user.trashedMemos.partials.index.trashed-memos-list')
      </div>
      {{-- ページネーション（ページが複数あるときのみ表示） --}}
      @if (isset($all_trashed_memos) && $all_trashed_memos->hasPages())
         <div class="mt-4">
            {{ $all_trashed_memos->links() }}
         </div>
      @endif
   </div>
   <script>
      'use strict'

      // 削除のアラート
      function deleteCheck() {
         const RESULT = confirm('本当に削除してもいいですか?');
         if (!RESULT) alert("削除をキャンセルしました");
         return RESULT;
      }
   </script>
</x-app-layout>
