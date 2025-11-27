'use strict'
// === 新規釣り場の追加 =====================================
document.addEventListener('DOMContentLoaded', () => {
   //追加ボタン、新規釣り場入力欄、釣り場セレクトボックスの要素の取得
   const addBtn = document.getElementById('add_spot_btn');
   const input = document.getElementById('new_spot_input');
   const select = document.getElementById('fishing_spot_select');
   if (!addBtn || !input || !select) return;

   // CSRFトークン取得
   const getCsrfToken = () => {
      const meta = document.querySelector('meta[name="csrf-token"]');
      if (meta) return meta.getAttribute('content');
      const tokenInput = document.querySelector('input[name="_token"]');
      return tokenInput ? tokenInput.value : '';
   };

   // メッセージ表示（フォーム内の表示領域に入れる）
   const messageEl = document.getElementById('spot_message');
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

   // 釣り場の追加の実行
   const addSpot = async (newSpot) => {
      const url = addBtn.dataset.url || "{{ route('user.spot.store') }}";
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
         // サーバーへ新規釣り場を送信する
         const res = await fetch(url, {
            method: 'POST',
            headers,
            body: JSON.stringify({
               spot_name: newSpot
            }),
         });

         // 成功: 釣り場選択欄に新しい option を追加
         if (res.status === 201) {
            const data = await res.json();
            const opt = document.createElement('option');
            opt.value = data.id;
            opt.textContent = data.name;
            select.appendChild(opt);
            input.value = '';
            showMessage('釣り場の選択肢に追加しました', 'success');
            setTimeout(clearMessage, 6000);
            return;
         }

         // 失敗: 422エラーメッセージを表示
            if (res.status === 422) {
            const data = await res.json().catch(() => ({}));
            const serverMsg = data?.errors?.spot_name?.[0] ?? data?.message;
            // 通常のバリデーションではじかれた場合のエラーメッセージ（保険）。
            showMessage(serverMsg ?? '入力に誤りがあります', 'error');
            return;
         }

         // 失敗: それ以外のエラーメッセージを表示
            try {
            const otherData = await res.json().catch(() => ({}));
            const otherMsg = otherData?.errors?.spot_name?.[0] ?? otherData?.message;
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
      const newSpot = input.value.trim();
      if (!newSpot) {
         showMessage('釣り場名を入力してください。', 'error');
         return;
      }
      addSpot(newSpot);
   });
});
