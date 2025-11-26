{{-- 戻るボタン --}}
<div class="flex justify-end">
   <button class="btn bg-gray-800 hover:bg-gray-700"
      onclick="location.href='{{ route('user.masters.index', ['tab' => $tab ?? 'spots']) }}'">
      戻る
   </button>
</div>
