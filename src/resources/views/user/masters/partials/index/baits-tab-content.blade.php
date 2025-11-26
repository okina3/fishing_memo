{{-- エサのタブ内容 --}}
@if ($tab === 'baits')
   {{-- タイトル --}}
   <h2 class="sub_heading mb-1">エサ名</h2>
   @foreach ($baits as $bait)
      <div class="py-3 flex justify-between items-center border-b border-slate-300">
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
@endif
