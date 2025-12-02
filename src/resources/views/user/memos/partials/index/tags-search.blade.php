{{-- タグ検索の表示エリア --}}
<section class="mb-2 md:mb-0 md:w-1/5 text-gray-600 border border-gray-500 rounded-lg overflow-hidden shadow">
   {{-- タイトル --}}
   <h1 class="heading heading_bg !leading-9">タグから検索</h1>
   {{-- タグの検索 --}}
   <div class="p-3 h-[15vh] md:h-[85vh] overflow-y-scroll overscroll-none bg-white">
      <div class="mb-2 hover:font-semibold"><a href="{{ route('user.index') }}">全てのメモを表示</a></div>
      {{-- タグ一覧 --}}
      @foreach ($all_tags as $tag)
         <a class="mb-1 block truncate hover:font-semibold" href="{{ route('user.index', ['tag' => $tag->id]) }}">
            {{ $tag->name }}
         </a>
      @endforeach
   </div>
</section>
