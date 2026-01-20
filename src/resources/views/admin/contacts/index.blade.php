<x-app-layout>
   <div class="p-2 bg-rose-100">
      {{-- フラッシュメッセージ --}}
      <x-common.flash-message status="session('status')" />
      {{-- 検索の表示エリア --}}
      <x-admin.search.contacts-search routeName="admin.contact.index" />
      {{-- ユーザーからの問い合わせ一覧の表示エリア --}}
      @include('admin.contacts.partials.index.contacts-list')
      {{-- ページネーション（ページが複数あるときのみ表示） --}}
      <div class="mt-4">
         @if (isset($all_contact) && $all_contact->hasPages())
            {{ $all_contact->links() }}
         @endif
      </div>
   </div>
</x-app-layout>
