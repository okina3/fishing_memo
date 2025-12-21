{{-- ブラウザの表示 --}}
<a data-micromodal-trigger="modal-add-rod" href='javascript:'
   class="btn-2 btn-bk bg-yellow-500 hover:bg-yellow-400">＋釣り竿を追加
</a>

{{-- 釣り竿追加モーダルウィンドウ --}}
<div class="modal micromodal-slide" id="modal-add-rod" aria-hidden="true">
   <div class="modal__overlay" tabindex="-1" data-micromodal-close>
      <div class="modal__container md:p-7" role="dialog" aria-modal="true" aria-labelledby="modal-add-rod-title">
         <header class="modal__header">
            {{-- モーダルウィンドウでのタイトル --}}
            <h2 class="modal__title" id="modal-add-rod-title">釣り竿を追加</h2>
            {{-- 閉じるボタン --}}
            <button type="button" class="modal__close" aria-label="Close modal" data-micromodal-close></button>
         </header>
         {{-- モーダルウィンドウでの内容 --}}
         <main class="modal__content" id="modal-add-rod-content">
            <div>
               <div class="flex gap-2 items-center">
                  <input id="rod_name_input" class="w-60 rounded" type="text" name="rod_name"
                     value="{{ old('rod_name') }}" placeholder="例:S社 サンプルロッド">
                  <button type="button" id="add_rod_btn" data-url="{{ route('user.rod.store.ajax') }}"
                     class="btn bg-blue-800 hover:bg-blue-700">
                     追加
                  </button>
               </div>
               {{-- エラーメッセージ（釣り竿を追加） --}}
               <x-input-error class="mt-2" :messages="$errors->get('rod_name')" />
               {{-- AJAX 用メッセージ表示領域 --}}
               <div id="rod_message" class="mt-2 text-sm" aria-live="polite"></div>
            </div>
         </main>
      </div>
   </div>
</div>

{{-- 固有の JavaScript の読み込み --}}
{{-- ajax-rod-add.js: 釣り竿を追加ボタンの AJAX 処理と UI 表示 --}}
@unless (app()->environment('testing'))
   @vite(['resources/js/user/memos/ajax-rod-add.js'])
@endunless
