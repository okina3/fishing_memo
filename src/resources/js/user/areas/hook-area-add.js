'use strict'
// === 釣り針入力エリア（最大5件） =====================================
// 定数・要素参照
const hookAreasContainer = document.getElementById('hook-areas-container');
const addHookAreaBtn = document.getElementById('add-hook-area');
const MAX_HOOK_AREAS = 5;

// 行を配列で取得
const getHookRows = () => Array.from(hookAreasContainer?.querySelectorAll('.hook-area-row') || []);

// 再インデックスとUI更新（簡潔版）
function updateHookControls() {
   const rows = getHookRows();
   rows.forEach((row, idx) => {
      // 各行内の select/input の name を必要なら更新
      row.querySelectorAll('select, input').forEach(el => {
         const name = el.getAttribute('name') || '';
         if (name.startsWith('hook_areas[')) {
            const newName = name.replace(/^hook_areas\[\d+\]/, `hook_areas[${idx}]`);
            el.setAttribute('name', newName);
         }
      });
      const del = row.querySelector('.remove-hook-area');
      if (del) del.classList.toggle('hidden', idx === 0);
   });
   const disabled = rows.length >= MAX_HOOK_AREAS;
   if (addHookAreaBtn) {
      addHookAreaBtn.disabled = disabled;
      addHookAreaBtn.classList.toggle('opacity-50', disabled);
      addHookAreaBtn.classList.toggle('cursor-not-allowed', disabled);
   }
}

// 削除（イベント委譲）
hookAreasContainer?.addEventListener('click', (e) => {
   const btn = e.target.closest?.('.remove-hook-area');
   if (!btn) return;
   const row = btn.closest('.hook-area-row');
   if (!row) return;
   // 最低1行を維持
   if (getHookRows().length <= 1) return;
   row.remove();
   updateHookControls();
});

// 行の追加（最初の行をクローンして値をクリア）
function addHookArea() {
   const rows = getHookRows();
   if (rows.length >= MAX_HOOK_AREAS) return;
   const base = rows[0];
   if (!base) return;
   const clone = base.cloneNode(true);
   clone.querySelectorAll('select, input').forEach(el => {
      if (el.tagName === 'SELECT') el.selectedIndex = 0;
      else el.value = '';
   });
   // 1行目以外は削除ボタンを表示
   const del = clone.querySelector('.remove-hook-area');
   if (del) del.classList.remove('hidden');
   hookAreasContainer.appendChild(clone);
   updateHookControls();
}

// 追加ボタン
addHookAreaBtn?.addEventListener('click', addHookArea);

// 初期化
if (hookAreasContainer) {
   updateHookControls();
}

/* eslint-disable no-undef */
