<x-app-layout>
   {{-- 釣り針名の編集 --}}
   <div class="px-2 py-2 bg-slate-200">
      <section class="text-gray-600 border border-gray-400 rounded-lg bg-white overflow-hidden">
         {{-- タイトル --}}
         <h1 class="heading heading_bg">釣り針の編集</h1>
         <div class="p-3">
            {{-- フラッシュメッセージ --}}
            <x-common.flash-message status="session('status')" />
            {{-- 選択した釣り針を編集するエリア --}}
            <form action="{{ route('user.hook.update') }}" method="POST">
               @csrf
               @method('patch')
               {{-- 現在の釣り針名の表示 --}}
               <div>
                  <h2 class="sub_heading-2 mb-2 block">現在の釣り針名</h2>
                  <div class="mb-6 p-2 w-full border border-gray-500 rounded">
                     {{ $hook->name }}
                  </div>
               </div>
               <div class="mb-10">
                  {{-- 新しい釣り針名（上書き） --}}
                  <label class="sub_heading mb-2 block">新しい釣り針名（上書き）</label>
                  <input class="w-full rounded" name="hook_name" type="text"
                     value="{{ old('hook_name', $hook->name) }}">
                  {{-- エラーメッセージ（釣り針の更新） --}}
                  <x-input-error class="mt-2" :messages="$errors->get('hook_name')" />
                  <input type="hidden" name="hookId" value="{{ $hook->id }}">
               </div>
               {{-- 更新ボタン --}}
               <div class="mb-5">
                  <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">更新する</button>
               </div>
            </form>
            {{-- 戻るボタン --}}
            <x-user.button.back-button-masters :tab="'hooks'" />
         </div>
      </section>
   </div>
</x-app-layout>
