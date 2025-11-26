{{-- 選択したメモの備考の表示 --}}
<div class="mb-5">
   <h2 class="sub_heading mb-1">備考</h2>
   <textarea class="w-full rounded" name="content" rows="7" placeholder="ここに入力">{{ $selectMemo->content }}</textarea>
   {{-- エラーメッセージ（メモの備考） --}}
   <x-input-error class="mt-2" :messages="$errors->get('content')" />
</div>
