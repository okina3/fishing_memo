{{-- 魚名のタブ内容 --}}
@if ($tab === 'fishNames')
   {{-- 魚名の登録フォーム --}}
   <div class="mb-5">
      <form action="{{ route('user.fish-name.store') }}" method="POST">
         @csrf
         <h2 class="sub_heading-2">魚名の登録</h2>
         <div class="flex gap-2 items-center">
            <input class="w-60 rounded" type="text" name="fish_name" value="{{ old('fish_name') }}" placeholder="例: マブナ">
            <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存</button>
         </div>
         {{-- エラーメッセージ（魚名の登録） --}}
         <x-input-error class="mt-2" :messages="$errors->get('fish_name')" />
      </form>
   </div>
   {{-- 既存の魚名一覧 --}}
   <h2 class="sub_heading-2 pb-1 border-b">魚名一覧</h2>
   @foreach ($fishNames as $fish)
      <div class="py-2 flex justify-between items-center border-b border-slate-300">
         {{-- 魚名の表示 --}}
         <p class="mr-5 md:w-[70%] truncate">
            {{ $fish->name }}
         </p>
         {{-- ボタンエリア --}}
         <div class="mt-2 md:w-[30%]">
            <div class="flex items-center gap-2">
               {{-- 編集ボタン --}}
               <form action="{{ route('user.fish-name.edit', ['fishName' => $fish->id]) }}" method="GET">
                  <button class="btn bg-violet-700 hover:bg-violet-500" type="submit">
                     編集
                  </button>
               </form>
               {{-- 削除ボタン --}}
               <form onsubmit="return deleteCheck()" action="{{ route('user.fish-name.destroy') }}" method="POST">
                  @csrf
                  @method('delete')
                  <input type="hidden" name="fishNameId" value="{{ $fish->id }}">
                  <button class="btn bg-red-600 hover:bg-red-500" type="submit">
                     削除
                  </button>
               </form>
            </div>
         </div>
      </div>
   @endforeach
   {{-- ページネーション --}}
   @if (method_exists($fishNames, 'links'))
      <div class="mt-4">
         {{ $fishNames->appends(request()->query())->links() }}
      </div>
   @endif
@endif
