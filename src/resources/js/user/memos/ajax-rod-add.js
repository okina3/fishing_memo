'use strict'
// === 新規釣り竿の追加 =====================================
document.addEventListener('DOMContentLoaded', () => {
   //追加ボタン、新規釣り竿名入力欄、釣り竿セレクトボックスの要素の取得
   const addBtn = document.getElementById('add_rod_btn');
   const input = document.getElementById('rod_name_input');
   const select = document.getElementById('rod-select');
   if (!addBtn || !input || !select) return;

   // CSRFトークン取得
   const getCsrfToken = () => {
      const meta = document.querySelector('meta[name="csrf-token"]');
      if (meta) return meta.getAttribute('content');
      const tokenInput = document.querySelector('input[name="_token"]');
      return tokenInput ? tokenInput.value : '';
   };

   // メッセージ表示（フォーム内の表示領域に入れる）
   const messageEl = document.getElementById('rod_message');
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

   // 釣り竿の追加の実行
   const addRod = async (newRod) => {
      const url = addBtn.dataset.url || '/rod/store-ajax';
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
         // サーバーへ新規釣り竿名を送信する
         const res = await fetch(url, {
            method: 'POST',
            headers,
            body: JSON.stringify({
               rod_name: newRod
            }),
         });

         // 成功: 釣り竿選択欄に新しい option を追加
         if (res.status === 201) {
            const data = await res.json();
            const opt = document.createElement('option');
            opt.value = data.id;
            opt.textContent = data.name;
            select.appendChild(opt);
            input.value = '';
            showMessage('釣り竿の選択肢に追加しました', 'success');
            setTimeout(clearMessage, 6000);
            return;
         }

         // 失敗: 422エラーメッセージを表示
         if (res.status === 422) {
            const data = await res.json().catch(() => ({}));
            const serverMsg = data?.errors?.rod_name?.[0] ?? data?.message;
            showMessage(serverMsg ?? '入力に誤りがあります', 'error');
            return;
         }

         // 失敗: それ以外のエラーメッセージを表示
         try {
            const otherData = await res.json().catch(() => ({}));
            const otherMsg = otherData?.errors?.rod_name?.[0] ?? otherData?.message;
            showMessage(otherMsg ?? '追加に失敗しました。時間をおいて再試行してください。', 'error');
         } catch (err) {
            showMessage('追加に失敗しました。時間をおいて再試行してください。', 'error');
         }

      } catch (e) {
         console.error(e);
         showMessage('通信エラーが発生しました', 'error');
      } finally {
         addBtn.disabled = false;
         addBtn.textContent = originalText;
      }
   };

   // 追加ボタンにクリックイベントリスナーを追加
   addBtn.addEventListener('click', () => {
      clearMessage();
      const newRod = input.value.trim();
      if (!newRod) {
         showMessage('釣り竿名を入力してください。', 'error');
         return;
      }
      addRod(newRod);
   });
});
