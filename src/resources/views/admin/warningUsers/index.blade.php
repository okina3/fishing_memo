<x-app-layout>
   <div class="px-2 py-2 bg-rose-100">
      {{-- フラッシュメッセージ --}}
      <x-common.flash-message status="session('status')" />
      {{-- ユーザーの検索の表示エリア --}}
      @include('admin.warningUsers.partials.users-search')
      {{-- 警告されたユーザー一覧の表示エリア --}}
      @include('admin.warningUsers.partials.warning-users-list')
   </div>
   {{-- ページネーション（ページが複数あるときのみ表示） --}}
   <div class="p-2 bg-white border-t border-gray-200">
      @if (isset($all_warning_users) && $all_warning_users->hasPages())
         {{ $all_warning_users->links() }}
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
