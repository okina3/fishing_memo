<x-app-layout>
   <div class="p-2 bg-rose-100">
      {{-- フラッシュメッセージ --}}
      <x-common.flash-message status="session('status')" />
      {{-- 検索の表示エリア --}}
      <x-admin.search.contacts-search routeName="admin.trashed-contact.index" />
      {{-- ユーザーからの問い合わせ一覧の表示エリア --}}
      @include('admin.trashedContacts.partials.contacts')
      {{-- ページネーション（ページが複数あるときのみ表示） --}}
      <div class="mt-4">
         @if (isset($all_trashed_contacts) && $all_trashed_contacts->hasPages())
            {{ $all_trashed_contacts->links() }}
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
