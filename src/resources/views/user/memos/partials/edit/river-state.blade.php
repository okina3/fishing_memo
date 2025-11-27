<div class="mb-8">
   {{-- 川の状態 --}}
   <h2 class="sub_heading mb-1">川の状態</h2>
   <div class="sm:flex-row sm:flex-wrap sm:gap-6 md:gap-8 flex flex-col items-start gap-6">
      {{-- 川の流れ --}}
      <div>
         <label class="mb-1 block text-sm text-gray-700">川の流れ</label>
         <select name="river_flow" class="w-32 rounded">
            <option value="" @selected(old('river_flow', optional($select_memo)->river_flow ?? '') === '')>
               未選択
            </option>
            <option value="流れあり" @selected(old('river_flow', optional($select_memo)->river_flow) === '流れあり')>
               流れあり
            </option>
            <option value="流れなし" @selected(old('river_flow', optional($select_memo)->river_flow) === '流れなし')>
               流れなし
            </option>
         </select>
         {{-- エラーメッセージ（川の流れ） --}}
         <x-input-error class="mt-2" :messages="$errors->get('river_flow')" />
      </div>
      {{-- 濁り --}}
      <div>
         <label class="mb-1 block text-sm text-gray-700">濁り</label>
         <select name="turbidity" class="w-32 rounded">
            <option value="" @selected(old('turbidity', optional($select_memo)->turbidity ?? '') === '')>
               未選択
            </option>
            <option value="クリア" @selected(old('turbidity', optional($select_memo)->turbidity) === 'クリア')>
               クリア
            </option>
            <option value="やや濁り" @selected(old('turbidity', optional($select_memo)->turbidity) === 'やや濁り')>
               やや濁り
            </option>
            <option value="濁り" @selected(old('turbidity', optional($select_memo)->turbidity) === '濁り')>
               濁り
            </option>
            <option value="強い濁り" @selected(old('turbidity', optional($select_memo)->turbidity) === '強い濁り')>
               強い濁り
            </option>
         </select>
         {{-- エラーメッセージ（濁り） --}}
         <x-input-error class="mt-2" :messages="$errors->get('turbidity')" />
      </div>
      {{-- 水中のゴミ --}}
      <div>
         <label class="mb-1 block text-sm text-gray-700">水中のゴミ</label>
         <select name="debris" class="w-32 rounded">
            <option value="" @selected(old('debris', optional($select_memo)->debris ?? '') === '')>
               未選択
            </option>
            <option value="なし" @selected(old('debris', optional($select_memo)->debris) === 'なし')>
               なし
            </option>
            <option value="ややあり" @selected(old('debris', optional($select_memo)->debris) === 'ややあり')>
               ややあり
            </option>
            <option value="あり" @selected(old('debris', optional($select_memo)->debris) === 'あり')>
               あり
            </option>
         </select>
         {{-- エラーメッセージ（水中のゴミ） --}}
         <x-input-error class="mt-2" :messages="$errors->get('debris')" />
      </div>
      {{-- 水位 --}}
      <div>
         <label class="mb-1 block text-sm text-gray-700">水位</label>
         <div class="flex items-center gap-2">
            <input class="w-24 rounded text-right" type="number" name="water_level"
               value="{{ old('water_level', optional($select_memo)->water_level) }}" placeholder="0.0"
               inputmode="decimal" step="0.1" min="0" max="999.9" />
            <span class="text-gray-600">m</span>
         </div>
         {{-- エラーメッセージ（水位） --}}
         <x-input-error class="mt-2" :messages="$errors->get('water_level')" />
      </div>
      {{-- 水温 --}}
      <div>
         <label class="mb-1 block text-sm text-gray-700">水温</label>
         <div class="flex items-center gap-2">
            <input class="w-24 rounded text-right" type="number" name="water_temp"
               value="{{ old('water_temp', optional($select_memo)->water_temp) }}" placeholder="0" inputmode="numeric"
               step="1" min="0" max="99" />
            <span class="text-gray-600">℃</span>
         </div>
         {{-- エラーメッセージ（水温） --}}
         <x-input-error class="mt-2" :messages="$errors->get('water_temp')" />
      </div>
   </div>
</div>
