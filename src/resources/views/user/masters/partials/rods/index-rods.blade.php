{{-- 釣り竿のタブ内容 --}}
@if ($tab === 'rods')
   {{-- 釣り竿の登録フォーム --}}
   <div class="mb-5">
      <form action="{{ route('user.rod.store') }}" method="POST">
         @csrf
         <h2 class="sub_heading-2">釣り竿の登録</h2>
         <div class="flex gap-2 items-center">
            <input class="w-60 rounded" type="text" name="rod_name" value="{{ old('rod_name') }}"
               placeholder="例: D社 サンプルロッド 12尺">
            <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存</button>
         </div>
         {{-- エラーメッセージ（釣り竿の登録） --}}
         <x-input-error class="mt-2" :messages="$errors->get('rod_name')" />
      </form>
   </div>
   {{-- 既存の釣り竿一覧 --}}
   <h2 class="sub_heading-2 pb-1 border-b">釣り竿一覧</h2>
   @foreach ($rods as $rod)
      <div class="py-2 flex justify-between items-center border-b border-slate-300">
         {{-- 釣り竿名の表示 --}}
         <p class="mr-5 md:w-[70%] truncate">
            {{ $rod->name }}
         </p>
         {{-- ボタンエリア --}}
         <div class="mt-2 md:w-[30%]">
            <div class="flex items-center gap-2">
               {{-- 編集ボタン --}}
               <form action="{{ route('user.rod.edit', ['rod' => $rod->id]) }}" method="GET">
                  <button class="btn bg-violet-700 hover:bg-violet-500" type="submit">
                     編集
                  </button>
               </form>
               {{-- 削除ボタン --}}
               <form onsubmit="return deleteCheck()" action="{{ route('user.rod.destroy') }}" method="POST">
                  @csrf
                  @method('delete')
                  <input type="hidden" name="rodId" value="{{ $rod->id }}">
                  <button class="btn bg-red-600 hover:bg-red-500" type="submit">
                     削除
                  </button>
               </form>
            </div>
         </div>
      </div>
   @endforeach
   {{-- ページネーション --}}
   @if (method_exists($rods, 'links'))
      <div class="mt-4">
         {{ $spots->appends(request()->query())->links() }}
      </div>
   @endif
@endif
