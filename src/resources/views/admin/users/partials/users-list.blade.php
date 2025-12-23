{{-- 登録ユーザー一覧の表示エリア --}}
<section class="text-gray-600 border border-gray-500 rounded-lg overflow-hidden">
   {{-- タイトル --}}
   <h1 class="heading heading_bg !bg-rose-900">ユーザー 一覧</h1>
   {{-- 登録ユーザー一覧 --}}
   <div class="p-2">
      @foreach ($all_users as $user)
         <div class="mb-3 p-2 md:flex justify-between items-center border border-gray-500 rounded-lg bg-white">
            <div class="md:w-4/5 font-semibold">
               {{-- ユーザーの名前 --}}
               <p class="mb-1 truncate">
                  ユーザー名<span class="font-normal">・・・・・・</span>
                  <span class="border-b border-slate-400">{{ $user->name }}</span>
               </p>
               {{-- ユーザーのメールアドレス --}}
               <p class="mb-1 truncate">
                  メールアドレス<span class="font-normal">・・・・</span>
                  <span class="border-b border-slate-400">{{ $user->email }}</span>
               </p>
            </div>
            {{-- 利用停止ボタン --}}
            <div class="md:w-1/5">
               <form class="md:flex justify-end" onsubmit="return deleteCheck()" action="{{ route('admin.destroy') }}"
                  method="post">
                  @csrf
                  @method('delete')
                  {{-- 選択されているユーザーのidを取得 --}}
                  <input type="hidden" name="userId" value="{{ $user->id }}">
                  <button class="btn bg-red-600 hover:bg-red-500" type="submit">利用停止</button>
               </form>
            </div>
         </div>
      @endforeach
   </div>
</section>
