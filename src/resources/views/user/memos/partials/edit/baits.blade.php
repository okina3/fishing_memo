<div class="mb-3">
   {{-- エサの入力 --}}
   <div class="pt-2 mb-1 flex items-center border-t border-gray-300">
      <h2 class="sub_heading-2">エサ</h2>
      {{-- エサの追加 --}}
      <x-user.ajax-add.bait-add />
   </div>

   @php
      // 初期表示行数（最低1、最大5）
      $oldBaits = old('baits');
      if (is_array($oldBaits)) {
          $existingBaits = $oldBaits;
      } else {
          $existingBaits = isset($select_memo) ? $select_memo->baits->pluck('id')->toArray() : [];
      }
      $initialRows = max(1, min(count($existingBaits), 5));
   @endphp
   <div>
      <div id="baits-container" class="space-y-1">
         @for ($i = 0; $i < $initialRows; $i++)
            <div class="flex flex-wrap items-center gap-x-6 gap-y-1 md:gap-x-10 bait-row">
               <select class="rounded w-60" name="baits[]">
                  <option value="">エサの選択してください</option>
                  @foreach ($all_baits as $bait)
                     <option value="{{ $bait->id }}" @selected(($existingBaits[$i] ?? '') == $bait->id)>
                        {{ $bait->name }}
                     </option>
                  @endforeach
               </select>
               <button type="button"
                  class="text-xs text-red-600 hover:underline remove-bait-row {{ $i === 0 ? 'hidden' : '' }}">
                  削除
               </button>
            </div>
         @endfor
      </div>
      <div class="mt-2">
         <button type="button" id="add-bait-row" class="text-sm text-blue-700 hover:underline">
            ＋ エサ入力エリアを追加（最大5件）
         </button>
      </div>
      {{-- エラーメッセージ（エサ配列） --}}
      <x-input-error class="mt-2" :messages="$errors->get('baits.*')" />
   </div>
</div>
{{-- 固有の JavaScript の読み込み --}}
{{-- bait-area-add.js: エサ入力エリアの追加/削除などの UI 制御 --}}
@unless (app()->environment('testing'))
   @vite(['resources/js/user/areas/bait-area-add.js'])
@endunless
