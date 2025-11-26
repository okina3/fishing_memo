{{-- タグを削除するエリア --}}
<form onsubmit="return deleteCheck()" action="{{ route('user.tag.destroy') }}" method="post">
   @csrf
   @method('delete')
   <div class="mb-5">
      {{-- タイトル --}}
      <h2 class="sub_heading mb-1">既存タグの削除</h2>
      <p class="mb-1 text-sm">
         ※タグ一覧から、削除したいタグをチェックして「タグを削除」を押してください。
      </p>
      <h2 class="sub_heading mb-1">タグ一覧</h2>
      {{-- タグ一覧 --}}
      @foreach ($all_tags as $tag)
         <div class="inline mr-3 border-b border-slate-500 hover:font-semibold">
            <input class="mb-1 rounded" type="checkbox" name="tags[]" id="{{ $tag->id }}"
               value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }} />
            <label for="{{ $tag->id }}">{{ $tag->name }}</label>
         </div>
      @endforeach
      {{-- エラーメッセージ （タグの削除） --}}
      <x-input-error class="mt-5" :messages="$errors->get('tags')" />
   </div>
   {{-- タグを削除するボタン --}}
   <button class="btn bg-red-600 hover:bg-red-500" type="submit">タグを削除</button>
</form>
