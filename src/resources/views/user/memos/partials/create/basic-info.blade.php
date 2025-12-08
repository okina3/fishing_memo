<div class="mb-8">
   {{-- 基本情報 --}}
   <h2 class="sub_heading mb-1">基本情報</h2>
   <div class="md:flex-row md:flex-wrap md:gap-8 flex flex-col items-start gap-6">
      <div class="sm:flex-row sm:gap-6 md:gap-8 flex flex-col items-start gap-4">
         {{-- 釣行日 --}}
         <div>
            <label class="mb-1 block text-sm text-gray-700">釣行日</label>
            <input class="rounded" type="date" name="fishing_date" value="{{ old('fishing_date') }}"
               max="{{ now()->toDateString() }}" />
            {{-- エラーメッセージ（釣行日） --}}
            <x-input-error class="mt-2" :messages="$errors->get('fishing_date')" />
         </div>
         {{-- 釣行時間 --}}
         <div>
            <label class="mb-1 block text-sm text-gray-700">釣行時間</label>
            <div class="flex items-center w-full">
               <input class="text-center rounded" type="time" name="start_time" value="{{ old('start_time') }}"
                  step="60" />
               <span class="my-0 mx-1 text-gray-600">〜</span>
               <input class="text-center rounded" type="time" name="end_time" value="{{ old('end_time') }}"
                  step="60" />
            </div>
            {{-- エラーメッセージ（釣行時間） --}}
            <x-input-error class="mt-2" :messages="$errors->get('start_time')" />
            <x-input-error class="mt-2" :messages="$errors->get('end_time')" />
         </div>
      </div>
      <div class="sm:flex-row flex flex-col items-start gap-4">
         {{-- 釣り場 --}}
         <div>
            <label class="mb-1 block text-sm text-gray-700">釣り場</label>
            <select name="fishing_spot" id="fishing_spot_select" class="w-60 rounded">
               <option value="" @selected(old('fishing_spot', '') == '')>
                  場所を選択してください
               </option>
               @foreach ($all_spots as $spot)
                  <option value="{{ $spot->id }}" @selected(old('fishing_spot') == $spot->id)>
                     {{ $spot->name }}
                  </option>
               @endforeach
            </select>
            {{-- エラーメッセージ（釣り場） --}}
            <x-input-error class="mt-2" :messages="$errors->get('fishing_spot')" />
         </div>
         {{-- 釣り場の追加 --}}
         <div>
            <h2 class="mb-1 block text-sm text-gray-700">（釣り場を選択肢に追加）</h2>
            <div class="flex gap-2 items-center">
               <input id="new_spot_input" class="w-60 rounded" type="text" name="spot_name"
                  value="{{ old('spot_name') }}" placeholder="例:相模川上流">
               <button type="button" id="add_spot_btn" data-url="{{ route('user.spot.store') }}"
                  class="btn-2 btn-bk bg-yellow-500 hover:bg-yellow-400">
                  追加
               </button>
            </div>
            {{-- エラーメッセージ（釣り場の追加） --}}
            <x-input-error class="mt-2" :messages="$errors->get('spot_name')" />
            {{-- AJAX 用メッセージ表示領域 --}}
            <div id="spot_message" class="mt-2 text-sm" aria-live="polite"></div>
         </div>
      </div>
   </div>
</div>
{{-- 固有の JavaScript の読み込み --}}
{{-- new-spot-add.js: 釣り場を追加ボタンの AJAX 処理と UI 表示 --}}
@unless (app()->environment('testing'))
   @vite(['resources/js/user/memos/new-spot-add.js'])
@endunless
