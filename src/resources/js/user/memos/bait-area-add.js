'use strict'
// === エサ入力エリア（最大5件） =====================================
// 定数・要素参照
const baitsContainer = document.getElementById('baits-container');
const addBaitRowBtn = document.getElementById('add-bait-row');
const MAX_BAIT_ROWS = 5;

// 行の取得
const getBaitRows = () => Array.from(baitsContainer?.querySelectorAll('.bait-row') || []);

// 再インデックスとUI更新
function updateBaitControls() {
   const rows = getBaitRows();
   // 削除ボタンの表示制御
   rows.forEach((row, idx) => {
      const del = row.querySelector('.remove-bait-row');
      if (del) del.classList.toggle('hidden', idx === 0);
   });
   const disabled = rows.length >= MAX_BAIT_ROWS;
   if (addBaitRowBtn) {
      addBaitRowBtn.disabled = disabled;
      addBaitRowBtn.classList.toggle('opacity-50', disabled);
      addBaitRowBtn.classList.toggle('cursor-not-allowed', disabled);
   }
}

// 削除（イベント委譲）
baitsContainer?.addEventListener('click', (e) => {
   const btn = e.target.closest?.('.remove-bait-row');
   if (!btn) return;
   const row = btn.closest('.bait-row');
   if (!row) return;
   // 最低1行を維持
   if (getBaitRows().length <= 1) return;
   row.remove();
   updateBaitControls();
});

// 行の追加（最初の行をクローンして値をクリア）
function addBaitRow() {
   const rows = getBaitRows();
   if (rows.length >= MAX_BAIT_ROWS) return;
   const base = rows[0];
   if (!base) return;
   const clone = base.cloneNode(true);
   clone.querySelectorAll('select, input').forEach(el => {
      if (el.tagName === 'SELECT') el.selectedIndex = 0;
      else el.value = '';
   });
   // 1行目以外は削除ボタンを表示
   const del = clone.querySelector('.remove-bait-row');
   if (del) del.classList.remove('hidden');
   baitsContainer.appendChild(clone);
   updateBaitControls();
}

// 追加ボタン
addBaitRowBtn?.addEventListener('click', addBaitRow);
// 初期化
updateBaitControls();