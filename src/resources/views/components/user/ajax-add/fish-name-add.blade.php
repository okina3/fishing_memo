{{-- ブラウザの表示 --}}
<a data-micromodal-trigger="modal-add-fish" href='javascript:'
   class="btn-2 btn-bk bg-yellow-500 hover:bg-yellow-400">＋魚名を登録
</a>

{{-- 魚名登録モーダルウィンドウ --}}
<div class="modal micromodal-slide" id="modal-add-fish" aria-hidden="true">
   <div class="modal__overlay" tabindex="-1" data-micromodal-close>
      <div class="modal__container md:p-7" role="dialog" aria-modal="true" aria-labelledby="modal-add-fish-title">
         <header class="modal__header">
            {{-- モーダルウィンドウでのタイトル --}}
            <h2 class="modal__title" id="modal-add-fish-title">魚名を登録</h2>
            {{-- 閉じるボタン --}}
            <button type="button" class="modal__close" aria-label="Close modal" data-micromodal-close></button>
         </header>
         {{-- モーダルウィンドウでの内容 --}}
         <main class="modal__content" id="modal-add-fish-content">
            <div>
               <div class="flex gap-2 items-center">
                  <input id="fish_name_input" class="w-60 rounded" type="text" name="fish_name"
                     value="{{ old('fish_name') }}" placeholder="例:コイ">
                  <button type="button" id="add_fish_btn" data-url="{{ route('user.fish-name.store.ajax') }}"
                     class="btn bg-blue-800 hover:bg-blue-700">
                     登録
                  </button>
               </div>
               <p class="mt-5 text-sm">＊登録すると選択エリアから選択可能になります。</p>
               {{-- エラーメッセージ（魚名を登録） --}}
               <x-input-error class="mt-2" :messages="$errors->get('fish_name')" />
               {{-- AJAX 用メッセージ表示領域 --}}
               <div id="fish_message" class="mt-2 text-sm" aria-live="polite"></div>
            </div>
         </main>
      </div>
   </div>
</div>

{{-- JavaScript の読み込み（AJAX 処理） --}}
@unless (app()->environment('testing'))
   @vite(['resources/js/user/ajax/ajax-fish-name-add.js'])
@endunless
