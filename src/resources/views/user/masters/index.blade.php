<x-app-layout>
   <div class="px-2 py-2 bg-slate-200">
      <section class="text-gray-600 border border-gray-400 rounded-lg  overflow-hidden">
         {{-- マスターズ管理ページのタイトル --}}
         <h1 class="heading heading_bg">マスターズ管理</h1>
         <div class="p-3 bg-white">
            {{-- フラッシュメッセージ --}}
            <x-common.flash-message status="session('status')" />
            {{-- タブ表示と検索エリア --}}
            @include('user.masters.partials.index.tab-search')
            {{-- 選択された各タブの内容表示エリア --}}
            {{-- 釣り場の表示 --}}
            @include('user.masters.partials.spots.index-spots')
            {{-- 釣り竿の表示 --}}
            @include('user.masters.partials.rods.index-rods')
            {{-- 釣り針の表示 --}}
            @include('user.masters.partials.hooks.index-hooks')
            {{-- エサの表示 --}}
            @include('user.masters.partials.baits.index-baits')
            {{-- 魚名の表示 --}}
            @include('user.masters.partials.fish-names.index-fish-names')
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
