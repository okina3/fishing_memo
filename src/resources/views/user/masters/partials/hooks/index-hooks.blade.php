{{-- 釣り針のタブ内容 --}}
@if ($tab === 'hooks')
   {{-- 釣り針の登録フォーム --}}
   <div class="mb-5">
      <form action="{{ route('user.hook.store') }}" method="POST">
         @csrf
         <h2 class="sub_heading-2">釣り針の登録</h2>
         <div class="flex gap-2 items-center">
            <input class="w-60 rounded" type="text" name="hook_name" value="{{ old('hook_name') }}"
               placeholder="例: G社 サンプル針 9号">
            <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存</button>
         </div>
         {{-- エラーメッセージ（釣り針の登録） --}}
         <x-input-error class="mt-2" :messages="$errors->get('hook_name')" />
      </form>
   </div>
   {{-- 既存の釣り針一覧 --}}
   <h2 class="sub_heading-2 pb-1 border-b">釣り針一覧</h2>
   @foreach ($hooks as $hook)
      <div class="py-2 flex justify-between items-center border-b border-slate-300">
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
