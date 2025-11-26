{{-- タグを新規作成するエリア --}}
<form action="{{ route('user.tag.store') }}" method="post">
   @csrf
   <div class="mb-10">
      {{-- タイトル --}}
      <h2 class="sub_heading mb-1">新規タグ作成</h2>
      {{-- 新規タグの入力 --}}
      <input class="mb-2 form-control rounded w-60" type="text" name="new_tag" placeholder="ここに新規タグを入力" />
      {{-- タグを保存するボタン --}}
      <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存</button>
      {{-- エラーメッセージ （新規タグ） --}}
      <x-input-error class="mt-2" :messages="$errors->get('new_tag')" />
   </div>
</form>
