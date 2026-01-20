<x-app-layout>
   <div class="p-2 bg-rose-100">
      {{-- フラッシュメッセージ --}}
      <x-common.flash-message status="session('status')" />
      {{-- 検索の表示エリア --}}
      <x-admin.search.users-search routeName="admin.warning.index" />
      {{-- 警告されたユーザー一覧の表示エリア --}}
      @include('admin.warningUsers.partials.warning-users-list')
      {{-- ページネーション（ページが複数あるときのみ表示） --}}
      <div class="mt-4">
         @if (isset($all_warning_users) && $all_warning_users->hasPages())
            {{ $all_warning_users->links() }}
         @endif
      </div>
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
