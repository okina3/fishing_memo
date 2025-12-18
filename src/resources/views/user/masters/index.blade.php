<x-app-layout>
   <div class="px-2 py-2 bg-slate-200">
      <section class="text-gray-600 border border-gray-400 rounded-lg  overflow-hidden">
         {{-- マスターズ管理ページのタイトル --}}
         <h1 class="heading heading_bg">マスターズ管理</h1>
         <div class="p-3 h-[85vh] overflow-y-scroll overscroll-none bg-white">
            {{-- フラッシュメッセージ --}}
            <x-common.flash-message status="session('status')" />
            {{-- 新規登録フォーム --}}
            @include('user.masters.partials.index.create-forms')
            {{-- タブ表示と検索エリア --}}
            @include('user.masters.partials.index.tab-list-search')
            {{-- 釣り場のタブ内容 --}}
            @include('user.masters.partials.index.spots-tab-content')
            {{-- 釣り竿のタブ内容 --}}
            @include('user.masters.partials.index.rods-tab-content')
            {{-- 釣り針のタブ内容 --}}
            @include('user.masters.partials.index.hooks-tab-content')
            {{-- エサのタブ内容 --}}
            @include('user.masters.partials.index.baits-tab-content')
            {{-- 魚名のタブ内容 --}}
            @include('user.masters.partials.index.fish-names-tab-content')
         </div>
      </section>
   </div>

   <script>
      'use strict'

      //削除のアラート
      function deleteCheck() {
         const RESULT = confirm('本当に削除してもいいですか? 完全に削除されます。');
         if (!RESULT) alert("削除をキャンセルしました");
         return RESULT;
      }
   </script>
</x-app-layout>
