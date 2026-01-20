<div class="mb-3">
   {{-- 釣り具の入力 --}}
   <div class="pt-2 flex items-center gap-20 border-t border-gray-300">
      <h2 class="sub_heading-2">釣り具</h2>
      {{-- 釣り竿の追加 --}}
      <x-user.ajax-add.rod-add />
   </div>

   @php
      // 初期表示行数（最低1、最大2）
      $initialRows = max(1, min(count(old('rod_areas', [])), 2));
   @endphp
   {{-- 釣り竿の入力 --}}
   <div id="rod-areas-container" class="space-y-5 lg:space-y-1">
      @for ($i = 0; $i < $initialRows; $i++)
         @php
            $entry = old('rod_areas', [])[$i] ?? [
                'rod_id' => '',
                'main_line' => '',
            ];
         @endphp
         <div class="flex flex-wrap items-center gap-x-6 gap-y-1 md:gap-x-10 rod-area-row">
            {{-- 釣り竿選択 --}}
            <div>
               <label class="block text-sm text-gray-700">釣り竿</label>
               <select name="rod_areas[{{ $i }}][rod_id]" class="w-60 rounded"
                  @if ($i === 0) id="rod-select" @endif>
                  <option value="">竿を選択してください</option>
                  @foreach ($all_rods as $rod)
                     <option value="{{ $rod->id }}" @selected(($entry['rod_id'] ?? '') == $rod->id)>{{ $rod->name }}</option>
                  @endforeach
               </select>
            </div>

            {{-- 道糸 --}}
            <div>
               <label class="block text-sm text-gray-700">道糸</label>
               <div class="flex items-center gap-2">
                  <input class="w-24 rounded text-right" type="number" name="rod_areas[{{ $i }}][main_line]"
                     value="{{ $entry['main_line'] ?? '' }}" placeholder="0.0" inputmode="decimal" step="0.1"
                     min="0" max="99.9" />
                  <span class="text-gray-600">号</span>
               </div>
            </div>

            {{-- 行削除ボタン --}}
            <button type="button"
               class="mt-6 text-xs text-red-600 hover:underline remove-rod-area {{ $i === 0 ? 'hidden' : '' }}">
               削除
            </button>
         </div>
      @endfor
   </div>

   {{-- エラーメッセージ（釣り具の内訳） --}}
   <x-input-error class="mt-2" :messages="$errors->get('rod_areas.*.rod_id')" />
   <x-input-error class="mt-2" :messages="$errors->get('rod_areas.*.main_line')" />
   <div class="mt-2">
      <button type="button" id="add-rod-area" class="text-sm text-blue-700 hover:underline">
         ＋釣り具入力エリア追加（最大2件）
      </button>
   </div>
</div>

{{-- 固有の JavaScript の読み込み --}}
@unless (app()->environment('testing'))
   @vite(['resources/js/user/areas/rod-area-add.js'])
@endunless
