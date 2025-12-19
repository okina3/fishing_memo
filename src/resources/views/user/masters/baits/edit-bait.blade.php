<x-app-layout>
   {{-- エサ名の編集 --}}
   <div class="px-2 py-2 bg-slate-200">
      <section class="text-gray-600 border border-gray-400 rounded-lg bg-white overflow-hidden">
         {{-- タイトル --}}
         <h1 class="heading heading_bg">エサの編集</h1>
         <div class="p-3">
            {{-- フラッシュメッセージ --}}
            <x-common.flash-message status="session('status')" />
            {{-- 選択したエサを編集するエリア --}}
            <form action="{{ route('user.bait.update') }}" method="POST" class="max-w-lg">
               @csrf
               @method('patch')
               <div>
                  {{-- 現在のエサ名の表示 --}}
                  <h2 class="sub_heading-2 mb-2 block">現在のエサ名</h2>
                  <div class="mb-6 p-2 w-full border border-gray-500 rounded">
                     {{ $bait->name }}
                  </div>
               </div>
               <div class="mb-10">
                  {{-- 新しいエサ名（上書き） --}}
                  <label class="sub_heading mb-2 block">新しいエサ名（上書き）</label>
                  <input class="w-full rounded" name="bait_name" type="text"
                     value="{{ old('bait_name', $bait->name) }}">
                  {{-- エラーメッセージ（エサの更新） --}}
                  <x-input-error class="mt-2" :messages="$errors->get('bait_name')" />
                  <input type="hidden" name="baitId" value="{{ $bait->id }}">
               </div>
               {{-- 更新ボタン --}}
               <div class="mb-5">
                  <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">更新する</button>
               </div>
            </form>
            {{-- 戻るボタン --}}
            <x-user.button.back-button-masters :tab="'baits'" />
         </div>
      </section>
   </div>
</x-app-layout>
