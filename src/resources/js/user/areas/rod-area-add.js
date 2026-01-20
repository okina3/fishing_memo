'use strict'
// === 釣り竿入力エリア（最大2件） =====================================
// 定数・要素参照
const rodAreasContainer = document.getElementById('rod-areas-container');
const addRodAreaBtn = document.getElementById('add-rod-area');
const MAX_ROD_AREAS = 2;

// 行を配列で取得
const getRodRows = () => Array.from(rodAreasContainer?.querySelectorAll('.rod-area-row') || []);

// 再インデックスとUI更新（簡潔版）
function updateRodControls() {
	const rows = getRodRows();
	rows.forEach((row, idx) => {
		// 各行内の select/input の name を必要なら更新
		row.querySelectorAll('select, input').forEach(el => {
			const name = el.getAttribute('name') || '';
			if (name.startsWith('rod_areas[')) {
				const newName = name.replace(/^rod_areas\[\d+\]/, `rod_areas[${idx}]`);
				el.setAttribute('name', newName);
			}
		});
		const del = row.querySelector('.remove-rod-area');
		if (del) del.classList.toggle('hidden', idx === 0);
	});
	const disabled = rows.length >= MAX_ROD_AREAS;
	if (addRodAreaBtn) {
		addRodAreaBtn.disabled = disabled;
		addRodAreaBtn.classList.toggle('opacity-50', disabled);
		addRodAreaBtn.classList.toggle('cursor-not-allowed', disabled);
	}
}

// 削除（イベント委譲）
rodAreasContainer?.addEventListener('click', (e) => {
	const btn = e.target.closest?.('.remove-rod-area');
	if (!btn) return;
	const row = btn.closest('.rod-area-row');
	if (!row) return;
	// 最低1行を維持
	if (getRodRows().length <= 1) return;
	row.remove();
	updateRodControls();
});

// 行の追加（最初の行をクローンして値をクリア）
function addRodArea() {
	const rows = getRodRows();
	if (rows.length >= MAX_ROD_AREAS) return;
	const base = rows[0];
	if (!base) return;
	const clone = base.cloneNode(true);
	clone.querySelectorAll('select, input').forEach(el => {
		if (el.tagName === 'SELECT') el.selectedIndex = 0;
		else el.value = '';
	});
	// 1行目以外は削除ボタンを表示
	const del = clone.querySelector('.remove-rod-area');
	if (del) del.classList.remove('hidden');
	rodAreasContainer.appendChild(clone);
	updateRodControls();
}

// 追加ボタン
addRodAreaBtn?.addEventListener('click', addRodArea);

// 初期化
if (rodAreasContainer) {
	updateRodControls();
}
