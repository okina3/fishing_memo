{{-- ソフトデリートメモ一覧の表示エリア --}}
<section class="text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
   {{-- タイトル --}}
   <h1 class="heading heading_bg">削除済みメモ一覧</h1>
   {{-- ソフトデリートメモ一覧 --}}
   <div class="p-2 bg-white">
      @foreach ($all_trashed_memos as $trashed_memo)
         <div class="py-3 md:flex justify-between items-center border-b border-slate-300">

            {{-- 釣行日、備考エリア --}}
            <div class="mr-5 md:w-[70%]">
               {{-- 釣行日 --}}
               <p class="sub_heading-2 mb-1 truncate">
                  {{ optional(optional($trashed_memo)->fishing_date)->format('Y-m-d') ?? '-' }}
               </p>
               {{-- 備考 --}}
               <p class="truncate">{{ optional($trashed_memo)->content ?? '' }}</p>
            </div>

            {{-- ボタンエリア --}}
            <div class="mt-2 md:w-[30%] flex md:justify-end">
               {{-- 元に戻すボタン --}}
               <form class="mr-3" action="{{ route('user.trashed-memo.undo') }}" method="post">
                  @csrf
                  @method('patch')
                  {{-- 選択されているメモのidを取得 --}}
                  <input type="hidden" name="memoId" value="{{ $trashed_memo->id }}">
                  <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">元に戻す</button>
               </form>
               {{-- 完全削除ボタン --}}
               <form onsubmit="return deleteCheck()" action="{{ route('user.trashed-memo.destroy') }}" method="post">
                  @csrf
                  @method('delete')
                  {{-- 選択されているメモのidを取得 --}}
                  <input type="hidden" name="memoId" value="{{ $trashed_memo->id }}">
                  <button type="submit" class="btn bg-red-600 hover:bg-red-500">完全削除</button>
               </form>
            </div>

         </div>
      @endforeach
   </div>
</section>
