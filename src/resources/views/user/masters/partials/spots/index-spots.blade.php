{{-- 釣り場のタブ内容 --}}
@if ($tab === 'spots')
   {{-- 釣り場の登録フォーム --}}
   <div class="mb-5">
      <form action="{{ route('user.spot.store') }}" method="POST">
         @csrf
         <h2 class="sub_heading-2">釣り場の登録</h2>
         <div class="flex gap-2 items-center">
            <input class="w-60 rounded" type="text" name="spot_name" value="{{ old('spot_name') }}"
               placeholder="例: T県 サンプル川上流域">
            <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存</button>
         </div>
         {{-- エラーメッセージ（釣り場の登録） --}}
         <x-input-error class="mt-2" :messages="$errors->get('spot_name')" />
      </form>
   </div>
   {{-- 既存の釣り場一覧 --}}
   <h2 class="sub_heading-2 pb-1 border-b">釣り場一覧</h2>
   @foreach ($spots as $spot)
      <div class="py-2 flex justify-between items-center border-b border-slate-300">
         {{-- 釣り場名の表示 --}}
         <p class="mr-5 md:w-[70%] truncate">
            {{ $spot->name }}
         </p>
         {{-- ボタンエリア --}}
         <div class="mt-2 md:w-[30%]">
            <div class="flex items-center gap-2">
               {{-- 編集ボタン --}}
               <form action="{{ route('user.spot.edit', ['spot' => $spot->id]) }}" method="GET">
                  <button class="btn bg-violet-700 hover:bg-violet-500" type="submit">
                     編集
                  </button>
               </form>
               {{-- 削除ボタン --}}
               <form onsubmit="return deleteCheck()" action="{{ route('user.spot.destroy') }}" method="POST">
                  @csrf
                  @method('delete')
                  <input type="hidden" name="spotId" value="{{ $spot->id }}">
                  <button class="btn bg-red-600 hover:bg-red-500" type="submit">
                     削除
                  </button>
               </form>
            </div>
         </div>
      </div>
   @endforeach
   {{-- ページネーション（ページが複数あるときのみ表示） --}}
   @if (isset($spots) && $spots->hasPages())
      <div class="mt-4">
         {{ $spots->links() }}
      </div>
   @endif
@endif
