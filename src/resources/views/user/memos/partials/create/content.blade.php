{{-- メモの備考入力 --}}
<div class="mb-5">
   <h2 class="sub_heading mb-1">備考</h2>
   <textarea class="w-full rounded" name="content" rows="7" placeholder="ここに入力">{{ old('content') }}</textarea>
   {{-- エラーメッセージ（メモの備考） --}}
   <x-input-error class="mt-2" :messages="$errors->get('content')" />
</div>
