<x-app-layout>
   <div class="px-2 py-2 bg-rose-100">
      {{-- フラッシュメッセージ --}}
      <x-common.flash-message status="session('status')" />
      {{-- 検索の表示エリア --}}
      <x-admin.search.contacts-search />
      {{-- ユーザーからの問い合わせ一覧の表示エリア --}}
      @include('admin.contacts.partials.index.contacts-list')
   </div>
</x-app-layout>
