<x-app-layout>
   {{-- 魚名の編集 --}}
   <div class="px-2 py-2 bg-slate-200">
      <section class="text-gray-600 border border-gray-400 rounded-lg bg-white overflow-hidden">
         {{-- タイトル --}}
         <h1 class="heading heading_bg">魚名の編集</h1>
         <div class="p-3">
            {{-- フラッシュメッセージ --}}
            <x-common.flash-message status="session('status')" />
            {{-- 選択した魚名を編集するエリア --}}
            <form action="{{ route('user.fish-name.update') }}" method="POST">
               @csrf
               @method('patch')
               <div>
                  {{-- 現在の魚名の表示 --}}
                  <h2 class="sub_heading-2 mb-2 block">現在の魚名</h2>
                  <div class="mb-6 p-2 w-full border border-gray-500 rounded">
                     {{ $fish_name->name }}
                  </div>
               </div>
               <div class="mb-10">
                  {{-- 新しい魚名（上書き） --}}
                  <label class="sub_heading mb-2 block">新しい魚名（上書き）</label>
                  <input class="w-full rounded" name="fish_name" type="text"
                     value="{{ old('fish_name', $fish_name->name) }}">
                  {{-- エラーメッセージ（魚名の更新） --}}
                  <x-input-error class="mt-2" :messages="$errors->get('fish_name')" />
                  <input type="hidden" name="fishNameId" value="{{ $fish_name->id }}">
               </div>
               {{-- 更新ボタン --}}
               <div class="mb-5">
                  <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">更新する</button>
               </div>
            </form>
            {{-- 戻るボタン --}}
            <x-user.button.back-button-masters :tab="'fishNames'" />
         </div>
      </section>
   </div>
</x-app-layout>
