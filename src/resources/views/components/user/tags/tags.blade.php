{{-- 選択したメモのタグを表示 --}}
<div class="mb-10">
   <h2 class="sub_heading mb-1">タグ</h2>
   <div class="flex flex-wrap gap-3">
      @foreach ($getMemoTagsName as $tag_name)
         <div class="flex items-center gap-1">
            <input class="mb-1 rounded" type="checkbox" checked disabled />
            {{ $tag_name }}
         </div>
      @endforeach
   </div>
</div>
