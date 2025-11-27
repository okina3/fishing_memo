'use strict'
// === 新規エサの追加 =====================================
document.addEventListener('DOMContentLoaded', () => {
   // 追加ボタン、新規エサ入力欄、エサセレクトボックスの要素の取得
   const addBtn = document.getElementById('add_bait_btn');
   const input = document.getElementById('new_bait_input');
   const baitsContainer = document.getElementById('baits-container');
   if (!addBtn || !input || !baitsContainer) return;

   // CSRFトークン取得
   const getCsrfToken = () => {
      const meta = document.querySelector('meta[name="csrf-token"]');
      if (meta) return meta.getAttribute('content');
      const tokenInput = document.querySelector('input[name="_token"]');
      return tokenInput ? tokenInput.value : '';
   };

   // メッセージ表示（フォーム内の表示領域に入れる）
   const messageEl = document.getElementById('bait_message');
   const clearMessage = () => {
      if (!messageEl) return;
      messageEl.textContent = '';
      messageEl.classList.remove('text-red-600', 'text-green-600');
   };

   // メッセージの表示色を決定する。（エラーは赤、成功は緑で表示）
   const showMessage = (message, type = 'error') => {
      if (!messageEl) return;
      messageEl.textContent = message || '';
      messageEl.classList.remove('text-red-600', 'text-green-600');
      if (type === 'error') {
         messageEl.classList.add('text-red-600');
      } else if (type === 'success') {
         messageEl.classList.add('text-green-600');
      }
   };

   // エサの追加の実行
   const addBait = async (newBait) => {
      const url = addBtn.dataset.url || "{{ route('user.bait.store') }}";
      const headers = {
         'Content-Type': 'application/json',
         'X-CSRF-TOKEN': getCsrfToken(),
         'Accept': 'application/json',
      };

      // ボタン無効化と表示を変更
      addBtn.disabled = true;
      const originalText = addBtn.textContent;
      addBtn.textContent = '追加中...';

      try {
         // サーバーへ新規エサを送信する
         const res = await fetch(url, {
            method: 'POST',
            headers,
            body: JSON.stringify({
               bait_name: newBait
            }),
         });

         // 成功: 全てのエサ選択欄に新しい option を追加
         if (res.status === 201) {
            const data = await res.json();
            const allSelects = Array.from(document.querySelectorAll('select[name="baits[]"]'));
            allSelects.forEach((s) => {
               const opt = document.createElement('option');
               opt.value = data.id;
               opt.textContent = data.name;
               s.appendChild(opt);
            });
            input.value = '';
            showMessage('エサの選択肢に追加しました', 'success');
            setTimeout(clearMessage, 6000);
            return;
         }

         // 失敗: 422エラーメッセージを表示
         if (res.status === 422) {
            const data = await res.json().catch(() => ({}));
            const serverMsg = data?.errors?.bait_name?.[0] ?? data?.message;
            // 通常のバリデーションではじかれた場合のエラーメッセージ（保険）。
            showMessage(serverMsg ?? '入力に誤りがあります', 'error');
            return;
         }

         // 失敗: それ以外のエラーメッセージを表示
         try {
            const otherData = await res.json().catch(() => ({}));
            const otherMsg = otherData?.errors?.bait_name?.[0] ?? otherData?.message;
            // 通常のバリデーションではじかれた場合のエラーメッセージ（保険）。
            showMessage(otherMsg ?? '追加に失敗しました。時間をおいて再試行してください。', 'error');
         } catch (err) {
            // 通常のバリデーションではじかれた場合のエラーメッセージ（保険）。
            showMessage('追加に失敗しました。時間をおいて再試行してください。', 'error');
         }

      } catch (e) {
         console.error(e);
         // 通常のバリデーションではじかれた場合のエラーメッセージ（保険）。
         showMessage('通信エラーが発生しました', 'error');
      } finally {
         addBtn.disabled = false;
         addBtn.textContent = originalText;
      }
   };

   // 追加ボタンにクリックイベントリスナーを追加
   addBtn.addEventListener('click', () => {
      // メッセージをクリアし、入力値をトリムしてサーバーへ送信
      clearMessage();
      const newBait = input.value.trim();
      if (!newBait) {
         showMessage('エサを入力してください。', 'error');
         return;
      }
      addBait(newBait);
   });
});