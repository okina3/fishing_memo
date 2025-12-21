<div class="mb-3">
   {{-- 仕掛けの入力 --}}
   <h2 class="sub_heading">仕掛け</h2>

   @php
      // 初期表示行数（最低1、最大5）
      $oldAreas = old('hook_areas');
      if (is_array($oldAreas)) {
          $existingAreas = $oldAreas;
      } else {
          $existingAreas = [];
          if (isset($select_memo) && $select_memo->hooks->isNotEmpty()) {
              foreach ($select_memo->hooks as $fn) {
                  $existingAreas[] = [
                      'hook_id' => $fn->id ?? '',
                      'leader_size' => $fn->pivot->leader_size ?? '',
                      'leader_upper_cm' => $fn->pivot->leader_upper_cm ?? '',
                      'leader_lower_cm' => $fn->pivot->leader_lower_cm ?? '',
                  ];
              }
          }
      }
      // 初期表示行数（最低1、最大5）
      $initialRows = max(1, min(count($existingAreas), 5));
   @endphp

   {{-- 釣り針の入力 --}}
   <div id="hook-areas-container" class="space-y-5 lg:space-y-1">
      @for ($i = 0; $i < $initialRows; $i++)
         @php
            $entry = $existingAreas[$i] ?? [
                'hook_id' => '',
                'leader_size' => '',
                'leader_upper_cm' => '',
                'leader_lower_cm' => '',
            ];
         @endphp
         <div class="flex flex-wrap items-center gap-x-6 gap-y-1 md:gap-x-10 hook-area-row">
            {{-- 釣り針選択 --}}
            <div>
               <label class="block text-sm text-gray-700">釣り針</label>
               <select name="hook_areas[{{ $i }}][hook_id]" class="w-60 rounded">
                  <option value="">針を選択してください</option>
                  @foreach ($all_hooks as $hook)
                     <option value="{{ $hook->id }}" @selected(($entry['hook_id'] ?? '') == $hook->id)>{{ $hook->name }}</option>
                  @endforeach
               </select>
            </div>

            {{-- ハリス（太さ） --}}
            <div>
               <label class="block text-sm text-gray-700">ハリス（太さ）</label>
               <div class="flex items-center gap-2">
                  <input class="w-24 rounded text-right" type="number"
                     name="hook_areas[{{ $i }}][leader_size]" value="{{ $entry['leader_size'] ?? '' }}"
                     placeholder="0.0" inputmode="decimal" step="0.1" min="0" max="99.9" />
                  <span class="text-gray-600">号</span>
               </div>
            </div>

            {{-- 上ハリス（長さ） --}}
            <div>
               <label class="block text-sm text-gray-700">上ハリス（長さ）</label>
               <div class="flex items-center gap-2">
                  <input class="w-24 rounded text-right" type="number"
                     name="hook_areas[{{ $i }}][leader_upper_cm]"
                     value="{{ $entry['leader_upper_cm'] ?? '' }}" placeholder="0" inputmode="numeric" step="1"
                     min="0" max="999" />
                  <span class="text-gray-600">cm</span>
               </div>
            </div>

            {{-- 下ハリス（長さ） --}}
            <div>
               <label class="block text-sm text-gray-700">下ハリス（長さ）</label>
               <div class="flex items-center gap-2">
                  <input class="w-24 rounded text-right" type="number"
                     name="hook_areas[{{ $i }}][leader_lower_cm]"
                     value="{{ $entry['leader_lower_cm'] ?? '' }}" placeholder="0" inputmode="numeric" step="1"
                     min="0" max="999" />
                  <span class="text-gray-600">cm</span>
               </div>
            </div>

            {{-- 行削除ボタン --}}
            <button type="button"
               class="mt-6 text-xs text-red-600 hover:underline remove-hook-area {{ $i === 0 ? 'hidden' : '' }}">
               削除
            </button>
         </div>
      @endfor
   </div>

   {{-- エラーメッセージ（仕掛けの内訳） --}}
   <x-input-error class="mt-2" :messages="$errors->get('hook_areas.*.hook_id')" />
   <x-input-error class="mt-2" :messages="$errors->get('hook_areas.*.leader_size')" />
   <x-input-error class="mt-2" :messages="$errors->get('hook_areas.*.leader_upper_cm')" />
   <x-input-error class="mt-2" :messages="$errors->get('hook_areas.*.leader_lower_cm')" />
   <div class="mt-2">
      <button type="button" id="add-hook-area" class="text-sm text-blue-700 hover:underline">
         ＋仕掛け入力エリア追加（最大5件）
      </button>
   </div>
</div>

{{-- 固有の JavaScript の読み込み --}}
@unless (app()->environment('testing'))
   @vite(['resources/js/user/areas/hook-area-add.js'])
@endunless
