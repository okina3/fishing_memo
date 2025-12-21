{{-- 共有中のメモ一覧の表示エリア --}}
<section class="md:ml-2 md:w-4/5 text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
   {{-- タイトル --}}
   <h1 class="heading heading_bg">共有されているメモ</h1>
   {{-- 共有中のメモ一覧 --}}
   <div class="p-2 h-[60vh] md:h-[85vh] overflow-y-scroll overscroll-none bg-white">
      @foreach ($shared_memos as $shared_memo)
         <div class="mb-3 p-2 border border-gray-400 rounded-lg">
            {{-- 共有メモの情報エリア --}}
            <div class="mb-2">
               {{-- 共有中のメモのユーザーの名前 --}}
               <div class="mb-2 font-semibold truncate">
                  <span class="text-blue-700 border-b border-slate-500">
                     {{ $shared_memo->user->name }}
                  </span>
                  <span class="ml-1">さん のメモ</span>
               </div>
               {{-- メモの釣行日 --}}
               <p class="sub_heading truncate">
                  {{ optional(optional($shared_memo)->fishing_date)->format('Y-m-d') ?? '-' }}
               </p>
               {{-- メモの備考 --}}
               <p class="truncate">
                  {{ optional($shared_memo)->content ?? '' }}
               </p>
            </div>
            {{-- ボタンエリア --}}
            <div class="flex justify-end text-white">
               {{-- メモの詳細ボタン --}}
               <button class="btn bg-sky-900 hover:bg-sky-700"
                  onclick="location.href='{{ route('user.share-setting.show', ['share' => $shared_memo->id]) }}'">
                  詳細
               </button>
               {{-- メモの編集ボタン --}}
               @if ($shared_memo->access)
                  <button class="btn ml-3 bg-violet-700 hover:bg-violet-500"
                     onclick="location.href='{{ route('user.share-setting.edit', ['share' => $shared_memo->id]) }}'">
                     編集
                  </button>
               @endif
            </div>
         </div>
      @endforeach
   </div>
</section>
