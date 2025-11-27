'use strict'
// === 釣果入力エリア（最大5件） =====================================
// 定数・要素参照
const catchesContainer = document.getElementById('catches-container');
const addCatchRowBtn = document.getElementById('add-catch-row');
const MAX_CATCH_ROWS = 5;

// 行の取得
const getCatchRows = () => Array.from(catchesContainer?.querySelectorAll('.catch-row') || []);

// 再インデックスとUI更新（簡潔版）
function updateCatchControls() {
   const rows = getCatchRows();
   rows.forEach((row, idx) => {
      // 各行内の select/input の name を必要なら更新
      row.querySelectorAll('select, input').forEach(el => {
         const name = el.getAttribute('name') || '';
         if (name.startsWith('fishing_results[')) {
            const newName = name.replace(/^fishing_results\[\d+\]/, `fishing_results[${idx}]`);
            el.setAttribute('name', newName);
         }
      });
      const del = row.querySelector('.remove-catch-row');
      if (del) del.classList.toggle('hidden', idx === 0);
   });
   const disabled = rows.length >= MAX_CATCH_ROWS;
   if (addCatchRowBtn) {
      addCatchRowBtn.disabled = disabled;
      addCatchRowBtn.classList.toggle('opacity-50', disabled);
      addCatchRowBtn.classList.toggle('cursor-not-allowed', disabled);
   }
}

// 削除（イベント委譲）
catchesContainer?.addEventListener('click', (e) => {
   const btn = e.target.closest?.('.remove-catch-row');
   if (!btn) return;
   const row = btn.closest('.catch-row');
   if (!row) return;
   // 最低1行を維持
   if (getCatchRows().length <= 1) return;
   row.remove();
   updateCatchControls();
});

// 行の追加（最初の行をクローンして値をクリア）
function addCatchRow() {
   const rows = getCatchRows();
   if (rows.length >= MAX_CATCH_ROWS) return;
   const base = rows[0];
   if (!base) return;
   const clone = base.cloneNode(true);
   clone.querySelectorAll('select, input').forEach(el => {
      if (el.tagName === 'SELECT') el.selectedIndex = 0;
      else el.value = '';
   });
   // 1行目以外は削除ボタンを表示
   const del = clone.querySelector('.remove-catch-row');
   if (del) del.classList.remove('hidden');
   catchesContainer.appendChild(clone);
   updateCatchControls();
}

// 追加ボタン
addCatchRowBtn?.addEventListener('click', addCatchRow);

// 初期化
if (catchesContainer) {
   updateCatchControls();
}