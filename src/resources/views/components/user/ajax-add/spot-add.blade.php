{{-- ブラウザの表示 --}}
<a data-micromodal-trigger="modal-add-spot" href='javascript:'
   class="btn-2 btn-bk bg-yellow-500 hover:bg-yellow-400">＋釣り場を追加
</a>

{{-- 釣り場追加モーダルウィンドウ --}}
<div class="modal micromodal-slide" id="modal-add-spot" aria-hidden="true">
   <div class="modal__overlay" tabindex="-1" data-micromodal-close>
      <div class="modal__container md:p-7" role="dialog" aria-modal="true" aria-labelledby="modal-add-spot-title">
         <header class="modal__header">
            {{-- モーダルウィンドウでのタイトル --}}
            <h2 class="modal__title" id="modal-add-spot-title">釣り場を追加</h2>
            {{-- 閉じるボタン --}}
            <button type="button" class="modal__close" aria-label="Close modal" data-micromodal-close></button>
         </header>
         {{-- モーダルウィンドウでの内容 --}}
         <main class="modal__content" id="modal-add-spot-content">
            <div>
               <div class="flex gap-2 items-center">
                  <input id="spot_input" class="w-60 rounded" type="text" name="spot_name"
                     value="{{ old('spot_name') }}" placeholder="例:T県 サンプル川上流域">
                  <button type="button" id="add_spot_btn" data-url="{{ route('user.spot.store.ajax') }}"
                     class="btn bg-blue-800 hover:bg-blue-700">
                     追加
                  </button>
               </div>
               {{-- エラーメッセージ（釣り場を追加） --}}
               <x-input-error class="mt-2" :messages="$errors->get('spot_name')" />
               {{-- AJAX 用メッセージ表示領域 --}}
               <div id="spot_message" class="mt-2 text-sm" aria-live="polite"></div>
            </div>
         </main>
      </div>
   </div>
</div>

{{-- JavaScript の読み込み（AJAX 処理） --}}
@unless (app()->environment('testing'))
   @vite(['resources/js/user/ajax/ajax-spot-add.js'])
@endunless
