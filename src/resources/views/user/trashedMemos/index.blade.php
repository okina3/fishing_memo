<x-app-layout>
   <div class="px-2 py-2 bg-slate-200">
      <section class="text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
         {{-- 削除済みメモの管理ページのタイトル --}}
         <h1 class="heading heading_bg">削除済みメモ一覧</h1>
         {{-- 削除済みメモを管理するエリア --}}
         <div class="p-3 h-[85vh] overflow-y-scroll overscroll-none bg-white">
            {{-- フラッシュメッセージ --}}
            <x-common.flash-message status="session('status')" />
            {{-- ソフトデリートされたメモ一覧 --}}
            @include('user.trashedMemos.partials.index.trashed-memos-list')
            {{-- 戻るボタン --}}
            <div class="mt-2"><x-user.button.back-button /></div>
         </div>
      </section>
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
