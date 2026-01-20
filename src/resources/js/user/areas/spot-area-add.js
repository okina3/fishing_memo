'use strict'
// === 釣り場入力エリア（最大2件） =====================================
// 定数・要素参照
const spotAreasContainer = document.getElementById('spot-areas-container');
const addSpotAreaBtn = document.getElementById('add-spot-area');
const MAX_SPOT_AREAS = 2;

// 行を配列で取得
const getSpotRows = () => Array.from(spotAreasContainer?.querySelectorAll('.spot-area-row') || []);

// 再インデックスとUI更新（簡潔版）
function updateSpotControls() {
   const rows = getSpotRows();
   rows.forEach((row, idx) => {
      // 各行内の select/input の name を必要なら更新
      row.querySelectorAll('select, input').forEach(el => {
         const name = el.getAttribute('name') || '';
         if (name.startsWith('spot_areas[')) {
            const newName = name.replace(/^spot_areas\[\d+\]/, `spot_areas[${idx}]`);
            el.setAttribute('name', newName);
         }
      });
      const del = row.querySelector('.remove-spot-area');
      if (del) del.classList.toggle('hidden', idx === 0);
   });
   const disabled = rows.length >= MAX_SPOT_AREAS;
   if (addSpotAreaBtn) {
      addSpotAreaBtn.disabled = disabled;
      addSpotAreaBtn.classList.toggle('opacity-50', disabled);
      addSpotAreaBtn.classList.toggle('cursor-not-allowed', disabled);
   }
}

// 削除（イベント委譲）
spotAreasContainer?.addEventListener('click', (e) => {
   const btn = e.target.closest?.('.remove-spot-area');
   if (!btn) return;
   const row = btn.closest('.spot-area-row');
   if (!row) return;
   // 最低1行を維持
   if (getSpotRows().length <= 1) return;
   row.remove();
   updateSpotControls();
});

// 行の追加（最初の行をクローンして値をクリア）
function addSpotArea() {
   const rows = getSpotRows();
   if (rows.length >= MAX_SPOT_AREAS) return;
   const base = rows[0];
   if (!base) return;
   const clone = base.cloneNode(true);
   clone.querySelectorAll('select, input').forEach(el => {
      if (el.tagName === 'SELECT') el.selectedIndex = 0;
      else el.value = '';
   });
   // 1行目以外は削除ボタンを表示
   const del = clone.querySelector('.remove-spot-area');
   if (del) del.classList.remove('hidden');
   spotAreasContainer.appendChild(clone);
   updateSpotControls();
}

// 追加ボタン
addSpotAreaBtn?.addEventListener('click', addSpotArea);

// 初期化
if (spotAreasContainer) {
   updateSpotControls();
}
