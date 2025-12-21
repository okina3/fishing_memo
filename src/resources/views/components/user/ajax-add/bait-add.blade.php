{{-- ブラウザの表示 --}}
<a data-micromodal-trigger="modal-add-bait" href='javascript:'
   class="btn-2 btn-bk bg-yellow-500 hover:bg-yellow-400">＋エサを登録
</a>

{{-- エサ登録モーダルウィンドウ --}}
<div class="modal micromodal-slide" id="modal-add-bait" aria-hidden="true">
   <div class="modal__overlay" tabindex="-1" data-micromodal-close>
      <div class="modal__container md:p-7" role="dialog" aria-modal="true" aria-labelledby="modal-add-bait-title">
         <header class="modal__header">
            {{-- モーダルウィンドウでのタイトル --}}
            <h2 class="modal__title" id="modal-add-bait-title">エサを登録</h2>
            {{-- 閉じるボタン --}}
            <button type="button" class="modal__close" aria-label="Close modal" data-micromodal-close></button>
         </header>
         {{-- モーダルウィンドウでの内容 --}}
         <main class="modal__content" id="modal-add-bait-content">
            <div>
               <div class="flex gap-2 items-center">
                  <input id="bait_input" class="w-60 rounded" type="text" name="bait_name"
                     value="{{ old('bait_name') }}" placeholder="例:アオイソメ">
                  <button type="button" id="add_bait_btn" data-url="{{ route('user.bait.store.ajax') }}"
                     class="btn bg-blue-800 hover:bg-blue-700">
                     登録
                  </button>
               </div>
               <p class="mt-5 text-sm">＊登録すると選択エリアから選択可能になります。</p>
               {{-- エラーメッセージ（エサを登録） --}}
               <x-input-error class="mt-2" :messages="$errors->get('bait_name')" />
               {{-- AJAX 用メッセージ表示領域 --}}
               <div id="bait_message" class="mt-2 text-sm" aria-live="polite"></div>
            </div>
         </main>
      </div>
   </div>
</div>

{{-- JavaScript の読み込み（AJAX 処理） --}}
@unless (app()->environment('testing'))
   @vite(['resources/js/user/ajax/ajax-bait-add.js'])
@endunless
