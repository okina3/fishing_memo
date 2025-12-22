<x-app-layout>
   <div class="px-2 py-2 bg-rose-100">
      {{-- フラッシュメッセージ --}}
      <x-common.flash-message status="session('status')" />
      {{-- ユーザーの検索の表示エリア --}}
      @include('admin.users.partials.users-search')
      {{-- 登録ユーザー一覧の表示エリア --}}
      @include('admin.users.partials.users-list')
   </div>
   {{-- ページネーション --}}
   <div class="p-2 bg-white border-t border-gray-200">
      {{ $all_users->links() }}
   </div>
   <script>
      'use strict'

      // 削除のアラート
      function deleteCheck() {
         const RESULT = confirm('本当に利用停止してもいいですか?');
         if (!RESULT) alert("キャンセルしました");
         return RESULT;
      }
   </script>
</x-app-layout>
