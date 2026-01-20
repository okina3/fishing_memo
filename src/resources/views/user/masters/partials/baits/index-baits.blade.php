{{-- エサのタブ内容 --}}
@if ($tab === 'baits')
   {{-- エサの登録フォーム --}}
   <div class="mb-5">
      <form action="{{ route('user.bait.store') }}" method="POST">
         @csrf
         <h2 class="sub_heading-2">エサの登録</h2>
         <div class="flex gap-2 items-center">
            <input class="w-60 rounded" type="text" name="bait_name" value="{{ old('bait_name') }}"
               placeholder="例: アオイソメ">
            <button type="submit" class="btn bg-blue-800 hover:bg-blue-700">保存</button>
         </div>
         {{-- エラーメッセージ（エサの登録） --}}
         <x-input-error class="mt-2" :messages="$errors->get('bait_name')" />
      </form>
   </div>
   {{-- 既存のエサ一覧 --}}
   <h2 class="sub_heading-2 pb-1 border-b">エサ一覧</h2>
   @foreach ($baits as $bait)
      <div class="py-2 flex justify-between items-center border-b border-slate-300">
         {{-- エサ名の表示 --}}
         <p class="mr-5 md:w-[70%] truncate">
            {{ $bait->name }}
         </p>
         {{-- ボタンエリア --}}
         <div class="mt-2 md:w-[30%]">
            <div class="flex items-center gap-2">
               {{-- 編集ボタン --}}
               <form action="{{ route('user.bait.edit', ['bait' => $bait->id]) }}" method="GET">
                  <button class="btn bg-violet-700 hover:bg-violet-500" type="submit">
                     編集
                  </button>
               </form>
               {{-- 削除ボタン --}}
               <form onsubmit="return deleteCheck()" action="{{ route('user.bait.destroy') }}" method="POST">
                  @csrf
                  @method('delete')
                  <input type="hidden" name="baitId" value="{{ $bait->id }}">
                  <button class="btn bg-red-600 hover:bg-red-500" type="submit">
                     削除
                  </button>
               </form>
            </div>
         </div>
      </div>
   @endforeach
   {{-- ページネーション（ページが複数あるときのみ表示） --}}
   @if (isset($baits) && $baits->hasPages())
      <div class="mt-4">
         {{ $baits->links() }}
      </div>
   @endif
@endif
