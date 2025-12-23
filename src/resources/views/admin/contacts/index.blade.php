<x-app-layout>
   <div class="px-2 py-2 bg-rose-100">
      {{-- フラッシュメッセージ --}}
      <x-common.flash-message status="session('status')" />
      {{-- 検索の表示エリア --}}
      <x-admin.search.contacts-search routeName="admin.contact.index" />
      {{-- ユーザーからの問い合わせ一覧の表示エリア --}}
      @include('admin.contacts.partials.index.contacts-list')
   </div>
   {{-- ページネーション（ページが複数あるときのみ表示） --}}
   <div class="p-2 bg-white border-t border-gray-200">
      @if (isset($all_contact) && $all_contact->hasPages())
         {{ $all_contact->links() }}
      @endif
   </div>
</x-app-layout>
