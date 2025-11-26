<x-app-layout>
   {{-- 釣り場名の編集 --}}
   <div class="px-2 py-2 bg-slate-200">
      <section class="text-gray-600 border border-gray-400 rounded-lg bg-white overflow-hidden">
         {{-- タイトル --}}
         <h1 class="heading heading_bg">釣り場の編集</h1>
         <div class="p-3">
            {{-- フラッシュメッセージ --}}
            <x-common.flash-message status="session('status')" />
            {{-- 選択した釣り場を編集するエリア --}}
            <form action="{{ route('user.spot.update') }}" method="POST">
               @csrf
               @method('patch')
               {{-- 現在の釣り場名の表示 --}}
               <div>
                  <h2 class="sub_heading mb-2 block">現在の釣り場名</h2>
                  <div class="mb-6 p-2 w-full border border-gray-500 rounded">
                     {{ $spot->name }}
                  </div>
               </div>
               <div class="mb-10">
                  {{-- 新しい釣り場名（上書き） --}}
                  <label class="sub_heading mb-2 block">新しい釣り場名（上書き）</label>
                  <input class="w-full rounded" name="spot_name" type="text"
                     value="{{ old('spot_name', $spot->name) }}">
                  {{-- エラーメッセージ（釣り場の更新） --}}
                  <x-input-error class="mt-2" :messages="$errors->get('spot_name')" />
                  <input type="hidden" name="spotId" value="{{ $spot->id }}">
               </div>
               {{-- 更新ボタン --}}
               <div class="mb-5">
                  <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">更新する</button>
               </div>
            </form>
            {{-- 戻るボタン --}}
            <x-user.button.back-button-masters :tab="'spots'" />
         </div>
      </section>
   </div>
</x-app-layout>
