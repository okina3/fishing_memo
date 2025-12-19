{{-- 釣り針のタブ内容 --}}
@if ($tab === 'hooks')
   {{-- タイトル --}}
   <h2 class="sub_heading mb-1">釣り針</h2>
   @foreach ($hooks as $hook)
      <div class="py-3 flex justify-between items-center border-b border-slate-300">
         {{-- 釣り針名の表示 --}}
         <p class="mr-5 md:w-[70%] truncate">
            {{ $hook->name }}
         </p>
         {{-- ボタンエリア --}}
         <div class="mt-2 md:w-[30%]">
            <div class="flex items-center gap-2">
               {{-- 編集ボタン --}}
               <form action="{{ route('user.hook.edit', ['hook' => $hook->id]) }}" method="GET">
                  <button class="btn bg-violet-700 hover:bg-violet-500" type="submit">
                     編集
                  </button>
               </form>
               {{-- 削除ボタン --}}
               <form onsubmit="return deleteCheck()" action="{{ route('user.hook.destroy') }}" method="POST">
                  @csrf
                  @method('delete')
                  <input type="hidden" name="hookId" value="{{ $hook->id }}">
                  <button class="btn bg-red-600 hover:bg-red-500" type="submit">
                     削除
                  </button>
               </form>
            </div>
         </div>
      </div>
   @endforeach
@endif
