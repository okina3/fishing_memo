<div class="mb-8">
   {{-- エサの入力 --}}
   <div class="md:flex-row flex flex-col items-start gap-4">
      <div>
         <h2 class="sub_heading mb-1">エサ</h2>
         @php
            // 初期表示行数（最低1、最大5）
            // 編集画面では、old() の値を優先し、なければ $select_memo に紐づいたエサを使う
            $oldBaits = old('baits');
            if (is_array($oldBaits)) {
                $existingBaits = $oldBaits;
            } else {
                $existingBaits = isset($select_memo) ? $select_memo->baits->pluck('id')->toArray() : [];
            }
            $initialRows = max(1, min(count($existingBaits), 5));
         @endphp
         <div>
            <div id="baits-container" class="space-y-2">
               @for ($i = 0; $i < $initialRows; $i++)
                  <div class="flex items-center gap-3 bait-row">
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
      {{-- エサの追加 --}}
      <div>
         <h2 class="mt-2 mb-1 block text-sm text-gray-700">（エサを選択肢に追加）</h2>
         <div class="flex gap-2 items-center">
            <input id="new_bait_input" class="w-60 rounded" type="text" name="bait_name"
               value="{{ old('bait_name') }}" placeholder="例: アオイソメ">
            <button type="button" id="add_bait_btn" data-url="{{ route('user.bait.store') }}"
               class="btn-2 btn-bk bg-yellow-500 hover:bg-yellow-400">
               追加
            </button>
         </div>
         {{-- エラーメッセージ（エサの追加） --}}
         <x-input-error class="mt-2" :messages="$errors->get('bait_name')" />
         {{-- AJAX 用メッセージ表示領域 --}}
         <div id="bait_message" class="mt-2 text-sm" aria-live="polite"></div>
      </div>
   </div>
</div>
{{-- 固有の JavaScript の読み込み --}}
{{-- new-bait-add.js: エサ追加ボタンの AJAX 処理と UI 表示 --}}
{{-- bait-area-add.js: エサ入力エリアの追加/削除などの UI 制御 --}}
@unless (app()->environment('testing'))
   @vite(['resources/js/user/memos/new-bait-add.js', 'resources/js/user/memos/bait-area-add.js'])
@endunless
