{{-- 選択したメモの備考を表示 --}}
<div class="mb-5">
   <h2 class="sub_heading mb-1">備考</h2>
   <textarea class="w-full rounded" name="content" rows="7" disabled>{{ optional($selectMemo)->content ?? '' }}</textarea>
</div>
