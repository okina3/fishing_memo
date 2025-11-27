<div class="mb-8">
   <div class="md:gap-8 md:flex-row md:flex-wrap lg:gap-12 flex flex-col items-start gap-6">
      {{-- 釣果の入力 --}}
      <div>
         <h2 class="sub_heading mb-1">釣果</h2>
         @php
            // 初期表示行数（最低1、最大5）
            // 編集画面では old() 優先、なければ $select_memo に紐づくピボットデータを使う
            $oldResults = old('fishing_results');
            if (is_array($oldResults)) {
                $existingResults = $oldResults;
            } else {
                $existingResults = [];
                if (isset($select_memo) && $select_memo->fish_names->isNotEmpty()) {
                    foreach ($select_memo->fish_names as $fn) {
                        $existingResults[] = [
                            'fish_name' => $fn->id,
                            'count' => $fn->pivot->count ?? '',
                            'length' => $fn->pivot->length ?? '',
                        ];
                    }
                }
            }
            $initialRows = max(1, min(count($existingResults), 5));
         @endphp
         <div class="flex items-start">
            <div id="catches-container" class="space-y-2 flex-1">
               @for ($i = 0; $i < $initialRows; $i++)
                  @php
                     $entry = $existingResults[$i] ?? ['fish_name' => '', 'count' => '', 'length' => ''];
                  @endphp
                  <div class="lg:gap-6 flex flex-wrap items-center gap-3 catch-row">
                     {{-- 魚名の選択 --}}
                     <div class="md:w-auto w-full">
                        <select class="w-60 rounded" name="fishing_results[{{ $i }}][fish_name]">
                           <option value="">魚名を選択してください</option>
                           @foreach ($all_fish_names as $fish)
                              <option value="{{ $fish->id }}" @selected(($entry['fish_name'] ?? '') == $fish->id)>{{ $fish->name }}
                              </option>
                           @endforeach
                        </select>
                     </div>
                     {{-- 釣果（匹） --}}
                     <div class="flex items-center gap-2">
                        <input class="md:w-24 w-20 rounded text-right" type="number"
                           name="fishing_results[{{ $i }}][count]" value="{{ $entry['count'] ?? '' }}"
                           placeholder="0" inputmode="numeric" min="0" step="1" />
                        <span class="text-gray-600">匹</span>
                     </div>
                     {{-- サイズ（cm） --}}
                     <div class="flex items-center gap-2">
                        <input class="md:w-24 w-20 rounded text-right" type="number"
                           name="fishing_results[{{ $i }}][length]" value="{{ $entry['length'] ?? '' }}"
                           placeholder="0" inputmode="numeric" min="0" step="1" />
                        <span class="text-gray-600">cm</span>
                     </div>
                     <button type="button"
                        class="text-xs text-red-600 hover:underline remove-catch-row {{ $i === 0 ? 'hidden' : '' }}">
                        削除
                     </button>
                  </div>
               @endfor
            </div>
         </div>
         {{-- エラーメッセージ（釣果の内訳） --}}
         <x-input-error class="mt-2" :messages="$errors->get('fishing_results.*.fish_name')" />
         <x-input-error class="mt-2" :messages="$errors->get('fishing_results.*.count')" />
         <x-input-error class="mt-2" :messages="$errors->get('fishing_results.*.length')" />
         <div class="mt-2">
            <button type="button" id="add-catch-row" class="text-sm text-blue-700 hover:underline">
               ＋釣果入力エリア追加（最大5件）
            </button>
         </div>
      </div>
      {{-- 魚名の追加 --}}
      <div>
         <h2 class="mt-2 mb-1 block text-sm text-gray-700">（魚名を選択肢に追加）</h2>
         <div class="flex gap-2 items-center">
            <input id="new_fish_input" class="w-60 rounded" type="text" name="fish_name"
               value="{{ old('fish_name') }}" placeholder="例: ヤマメ">
            <button type="button" id="add_fish_btn" data-url="{{ route('user.fish-name.store') }}"
               class="btn-2 btn-bk bg-yellow-500 hover:bg-yellow-400">
               追加
            </button>
         </div>
         {{-- エラーメッセージ（魚名の追加） --}}
         <x-input-error class="mt-2" :messages="$errors->get('fish_name')" />
         {{-- AJAX 用メッセージ表示領域 --}}
         <div id="fish_message" class="mt-2 text-sm" aria-live="polite"></div>
      </div>
   </div>
</div>
{{-- 固有の JavaScript の読み込み --}}
{{-- new-fish-name-add.js: 魚名追加ボタンの AJAX 処理と UI 表示 --}}
{{-- fishing-result-add.js: 釣果入力エリアの追加/削除などの UI 制御 --}}
@vite(['resources/js/user/memos/new-fish-name-add.js', 'resources/js/user/memos/fishing-result-add.js'])
