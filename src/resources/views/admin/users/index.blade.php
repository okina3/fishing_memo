<x-app-layout>
   <div class="p-2 bg-rose-100">
      {{-- フラッシュメッセージ --}}
      <x-common.flash-message status="session('status')" />
      {{-- 検索の表示エリア --}}
      <x-admin.search.users-search routeName="admin.index" />
      {{-- 登録ユーザー一覧の表示エリア --}}
      @include('admin.users.partials.users-list')
      {{-- ページネーション（ページが複数あるときのみ表示） --}}
      <div class="mt-4">
         @if (isset($all_users) && $all_users->hasPages())
            {{ $all_users->links() }}
         @endif
      </div>
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
