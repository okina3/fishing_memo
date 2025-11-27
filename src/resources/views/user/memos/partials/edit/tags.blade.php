{{-- 選択したメモに紐づいた既存タグを表示 --}}
<div class="mb-10">
   <h2 class="sub_heading mb-1">既存タグの選択</h2>
   <div class="flex flex-wrap gap-3">
      @foreach ($all_tags as $tag)
         <div class="flex items-center gap-1 hover:font-semibold">
            <input class="mb-1 rounded" type="checkbox" name="tags[]" id="{{ $tag->id }}" value="{{ $tag->id }}"
               {{ in_array($tag->id, $get_memo_tags_id) ? 'checked' : '' }} />
            <label for="{{ $tag->id }}">{{ $tag->name }}</label>
         </div>
      @endforeach
   </div>
</div>
