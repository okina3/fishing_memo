<x-app-layout>
   <div class="px-2 py-2 bg-slate-200">
      {{-- フラッシュメッセージ --}}
      <x-common.flash-message status="session('status')" />
      <div class="mb-2 md:flex justify-between">
         {{-- ユーザー検索の表示エリア --}}
         @include('user.shareSettings.partials.index.users-search')
         {{-- 共有中のメモ一覧の表示エリア --}}
         @include('user.shareSettings.partials.index.share-memos-list')
      </div>
   </div>
</x-app-layout>
