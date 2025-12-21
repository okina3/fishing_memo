<div class="mb-3">
   {{-- 釣果の入力 --}}
   <h2 class="sub_heading">釣果</h2>

   @php
      // 初期表示行数（最低1、最大5）
      $oldResults = old('fishing_results');
      if (is_array($oldResults)) {
          $existingResults = $oldResults;
      } else {
          $existingResults = [];
          if (isset($select_memo) && $select_memo->fish_names->isNotEmpty()) {
              foreach ($select_memo->fish_names as $fn) {
                  $existingResults[] = [
                      'fish_name_id' => $fn->id,
                      'count' => $fn->pivot->count ?? '',
                      'length' => $fn->pivot->length ?? '',
                  ];
              }
          }
      }
      $initialRows = max(1, min(count($existingResults), 5));
   @endphp

   <div id="catches-container" class="space-y-5 lg:space-y-1">
      @for ($i = 0; $i < $initialRows; $i++)
         @php
            $entry = $existingResults[$i] ?? [
                'fish_name_id' => '',
                'count' => '',
                'length' => '',
            ];
         @endphp
         <div class="flex flex-wrap items-center gap-x-6 gap-y-1 md:gap-x-10 catch-row">
            {{-- 魚名の選択 --}}
            <div>
               <label class="block text-sm font-semibold text-gray-700">魚名</label>
               <select class="w-60 rounded" name="fishing_results[{{ $i }}][fish_name_id]">
                  <option value="">魚名を選択してください</option>
                  @foreach ($all_fish_names as $fish)
                     <option value="{{ $fish->id }}" @selected(($entry['fish_name_id'] ?? '') == $fish->id)>{{ $fish->name }}
                     </option>
                  @endforeach
               </select>
            </div>

            {{-- 釣果（匹） --}}
            <div>
               <label class="block text-sm text-gray-700">匹数</label>
               <div class="flex items-center gap-2">
                  <input class="md:w-24 w-20 rounded text-right" type="number"
                     name="fishing_results[{{ $i }}][count]" value="{{ $entry['count'] ?? '' }}"
                     placeholder="0" inputmode="numeric" min="0" step="1" />
                  <span class="text-gray-600">匹</span>
               </div>
            </div>

            {{-- サイズ（cm） --}}
            <div>
               <label class="block text-sm text-gray-700">最大サイズ</label>
               <div class="flex items-center gap-2">
                  <input class="md:w-24 w-20 rounded text-right" type="number"
                     name="fishing_results[{{ $i }}][length]" value="{{ $entry['length'] ?? '' }}"
                     placeholder="0" inputmode="numeric" min="0" step="1" />
                  <span class="text-gray-600">cm</span>
               </div>
            </div>

            {{-- 行削除ボタン --}}
            <button type="button"
               class="mt-6 text-xs text-red-600 hover:underline remove-catch-row {{ $i === 0 ? 'hidden' : '' }}">
               削除
            </button>
         </div>
      @endfor
   </div>

   {{-- エラーメッセージ（釣果の内訳） --}}
   <x-input-error class="mt-2" :messages="$errors->get('fishing_results.*.fish_name_id')" />
   <x-input-error class="mt-2" :messages="$errors->get('fishing_results.*.count')" />
   <x-input-error class="mt-2" :messages="$errors->get('fishing_results.*.length')" />
   <div class="mt-2">
      <button type="button" id="add-catch-row" class="text-sm text-blue-700 hover:underline">
         ＋釣果入力エリア追加（最大5件）
      </button>
   </div>
</div>
{{-- 固有の JavaScript の読み込み --}}
{{-- fishing-result-add.js: 釣果入力エリアの追加/削除などの UI 制御 --}}
@unless (app()->environment('testing'))
   @vite(['resources/js/user/areas/fishing-result-add.js'])
@endunless
