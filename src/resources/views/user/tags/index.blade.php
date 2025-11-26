<x-app-layout>
   <div class="px-2 py-2 bg-slate-200">
      <section class="text-gray-600 border border-gray-400 rounded-lg bg-white overflow-hidden">
         {{-- タグの管理ページのタイトル --}}
         <h1 class="heading heading_bg">タグ一覧</h1>
         {{-- タグを管理するエリア --}}
         <div class="p-3 h-[85vh] overflow-y-scroll overscroll-none">
            {{-- フラッシュメッセージ --}}
            <x-common.flash-message status="session('status')" />
            {{-- タグを新規作成するエリア --}}
            @include('user.tags.partials.index.new-tags')
            {{-- タグを削除するエリア --}}
            @include('user.tags.partials.index.tags-delete')
            {{-- 戻るボタン --}}
            <x-user.button.back-button />
         </div>
      </section>
   </div>
   <script>
      'use strict'

      //削除のアラート
      function deleteCheck() {
         const RESULT = confirm('本当に削除してもいいですか?');
         if (!RESULT) alert("削除をキャンセルしました");
         return RESULT;
      }
   </script>
</x-app-layout>
