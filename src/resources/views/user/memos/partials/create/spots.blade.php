<div class="mb-8">
   {{-- 釣り場の入力 --}}
   <h2 class="sub_heading mb-1">場所</h2>

   @php
      // 初期表示行数（最低1、最大3）
      $initialRows = max(1, min(count(old('spot_areas', [])), 3));
   @endphp

   <div id="spot-areas-container" class="space-y-2">
      @for ($i = 0; $i < $initialRows; $i++)
         @php
            $entry = old('spot_areas', [])[$i] ?? [
                'spot_id' => '',
                'river_flow' => '',
                'turbidity' => '',
                'water_level' => '',
                'water_temp' => '',
            ];
         @endphp
         <div class="flex flex-wrap items-center gap-10 spot-area-row">
            {{-- 釣り場選択 --}}
            <div>
               <label class="mb-1 block text-sm text-gray-700">釣り場</label>
               <select name="spot_areas[{{ $i }}][spot_id]" class="w-60 rounded">
                  <option value="">場所を選択してください</option>
                  @foreach ($all_spots as $spot)
                     <option value="{{ $spot->id }}" @selected(($entry['spot_id'] ?? '') == $spot->id)>{{ $spot->name }}</option>
                  @endforeach
               </select>
            </div>

            {{-- 流れの有無 --}}
            <div>
               <label class="mb-1 block text-sm text-gray-700">流れの有無</label>
               <select name="spot_areas[{{ $i }}][river_flow]" class="w-32 rounded">
                  <option value="" @selected(($entry['river_flow'] ?? '') === '')>未選択</option>
                  <option value="流れあり" @selected(($entry['river_flow'] ?? '') === '流れあり')>あり</option>
                  <option value="流れなし" @selected(($entry['river_flow'] ?? '') === '流れなし')>なし</option>
               </select>
            </div>

            {{-- 濁り --}}
            <div>
               <label class="mb-1 block text-sm text-gray-700">濁り</label>
               <select name="spot_areas[{{ $i }}][turbidity]" class="w-32 rounded">
                  <option value="" @selected(($entry['turbidity'] ?? '') === '')>未選択</option>
                  <option value="クリア" @selected(($entry['turbidity'] ?? '') === 'クリア')>クリア</option>
                  <option value="濁り" @selected(($entry['turbidity'] ?? '') === '濁り')>濁り</option>
               </select>
            </div>

            {{-- 水深 --}}
            <div>
               <label class="mb-1 block text-sm text-gray-700">水深</label>
               <div class="flex items-center gap-2">
                  <input class="w-24 rounded text-right" type="number"
                     name="spot_areas[{{ $i }}][water_level]" value="{{ $entry['water_level'] ?? '' }}"
                     placeholder="0.0" inputmode="decimal" step="0.1" min="0" max="999.9" />
                  <span class="text-gray-600">m</span>
               </div>
            </div>

            {{-- 水温 --}}
            <div>
               <label class="mb-1 block text-sm text-gray-700">水温</label>
               <div class="flex items-center gap-2">
                  <input class="w-24 rounded text-right" type="number"
                     name="spot_areas[{{ $i }}][water_temp]" value="{{ $entry['water_temp'] ?? '' }}"
                     placeholder="0" inputmode="numeric" step="1" min="0" max="99" />
                  <span class="text-gray-600">℃</span>
               </div>
            </div>

            {{-- 行削除ボタン --}}
            <button type="button"
               class="text-xs text-red-600 hover:underline remove-spot-area {{ $i === 0 ? 'hidden' : '' }}">
               削除
            </button>
         </div>
      @endfor
   </div>

   {{-- エラーメッセージ（釣り場の内訳） --}}
   <x-input-error class="mt-2" :messages="$errors->get('spot_areas.*.spot_id')" />
   <x-input-error class="mt-2" :messages="$errors->get('spot_areas.*.river_flow')" />
   <x-input-error class="mt-2" :messages="$errors->get('spot_areas.*.turbidity')" />
   <x-input-error class="mt-2" :messages="$errors->get('spot_areas.*.water_level')" />
   <x-input-error class="mt-2" :messages="$errors->get('spot_areas.*.water_temp')" />
   <div class="mt-2">
      <button type="button" id="add-spot-area" class="text-sm text-blue-700 hover:underline">
         ＋場所入力エリア追加（最大3件）
      </button>
   </div>
</div>

{{-- 固有の JavaScript の読み込み --}}
@unless (app()->environment('testing'))
   @vite(['resources/js/user/memos/spot-area-add.js'])
@endunless
