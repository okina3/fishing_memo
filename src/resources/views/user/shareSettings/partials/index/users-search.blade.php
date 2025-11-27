{{-- ユーザー検索の表示エリア --}}
<section class="mb-2 md:mb-0 md:w-1/5 text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
   {{-- タイトル --}}
   <h1 class="heading heading_bg">ユーザーから検索</h1>
   {{-- ユーザーの検索 --}}
   <div class="p-3 h-[15vh] md:h-[85vh] overflow-y-scroll overscroll-none bg-white">
      <div class="mb-2 hover:font-semibold"><a href="share-setting/">全てのメモを表示</a></div>
      {{-- ユーザー一覧 --}}
      @foreach ($shared_users as $shared_user)
         {{-- 暗号化してurlに値を渡す --}}
         <a class="mb-1 block truncate hover:font-semibold" href="share-setting/?user={{ encrypt($shared_user->id) }}">
            {{ $shared_user->name }}
         </a>
      @endforeach
   </div>
</section>
