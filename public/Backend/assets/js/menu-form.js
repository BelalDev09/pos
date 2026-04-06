/**
 * menu-form.js
 * Create / Edit Menu Item — interactive behaviors
 * Handles: image drag-drop, size rows, ingredient search, size preview chips
 */

(function () {
    'use strict';

    /* ═══════════════════════════════════════════════════════════
       1. IMAGE UPLOAD — drag-drop + preview
    ══════════════════════════════════════════════════════════════ */
    const dropZone = document.getElementById('imageDropZone');
    const imageInput = document.getElementById('imageInput');
    const previewGrid = document.getElementById('imagePreviewGrid');

    if (dropZone && imageInput) {
        // Click on zone → trigger file picker
        dropZone.addEventListener('click', e => {
            if (e.target.tagName !== 'LABEL') imageInput.click();
        });

        // Drag events
        ['dragenter', 'dragover'].forEach(ev => {
            dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
        });
        ['dragleave', 'drop'].forEach(ev => {
            dropZone.addEventListener(ev, () => dropZone.classList.remove('drag-over'));
        });
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            handleFiles(e.dataTransfer.files);
        });

        imageInput.addEventListener('change', () => handleFiles(imageInput.files));
    }

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = e => addPreviewThumb(e.target.result, file.name);
            reader.readAsDataURL(file);
        });
    }

    function addPreviewThumb(src, name) {
        if (!previewGrid) return;
        const thumb = document.createElement('div');
        thumb.className = 'preview-thumb';
        thumb.innerHTML = `
      <img src="${src}" alt="${name}" />
      <button type="button" class="remove-preview" title="Remove" aria-label="Remove image">×</button>
    `;
        thumb.querySelector('.remove-preview').addEventListener('click', () => thumb.remove());
        previewGrid.appendChild(thumb);
    }

    /* ═══════════════════════════════════════════════════════════
       2. SIZE ROWS — add / remove + live preview chips
    ══════════════════════════════════════════════════════════════ */
    const sizeRows = document.getElementById('sizeRows');
    const addSizeBtn = document.getElementById('addSizeBtn');
    const chipsWrap = document.getElementById('sizeChipsPreview');
    let sizeIndex = sizeRows ? sizeRows.querySelectorAll('.size-row').length : 0;

    if (addSizeBtn && sizeRows) {
        addSizeBtn.addEventListener('click', addSizeRow);
        // Remove buttons (delegated)
        sizeRows.addEventListener('click', e => {
            if (e.target.closest('.remove-size-btn')) {
                const row = e.target.closest('.size-row');
                if (sizeRows.querySelectorAll('.size-row').length > 1) {
                    row.style.opacity = '0';
                    row.style.transform = 'translateY(-6px)';
                    row.style.transition = 'all 0.18s ease';
                    setTimeout(() => { row.remove(); rebuildSizeIndexes(); renderSizeChips(); }, 180);
                }
            }
        });
        // Live update chips on input
        sizeRows.addEventListener('input', renderSizeChips);
        renderSizeChips(); // initial render
    }

    function addSizeRow() {
        const div = document.createElement('div');
        div.className = 'size-row';
        div.dataset.index = sizeIndex;
        div.innerHTML = `
      <div class="field-group field-group--row">
        <div class="field">
          <label class="field-label">Label</label>
          <input type="text" name="sizes[${sizeIndex}][label]" class="field-input" placeholder="e.g. Medium" />
        </div>
        <div class="field field--sm">
          <label class="field-label">Price ($)</label>
          <input type="number" name="sizes[${sizeIndex}][price]" class="field-input" placeholder="0.00" step="0.01" min="0" />
        </div>
        <button type="button" class="remove-row-btn remove-size-btn" title="Remove">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>`;
        sizeRows.appendChild(div);
        sizeIndex++;
        div.querySelector('input').focus();
    }

    function rebuildSizeIndexes() {
        sizeRows.querySelectorAll('.size-row').forEach((row, i) => {
            row.dataset.index = i;
            row.querySelectorAll('input').forEach(inp => {
                inp.name = inp.name.replace(/sizes\[\d+\]/, `sizes[${i}]`);
            });
        });
    }

    function renderSizeChips() {
        if (!chipsWrap) return;
        const rows = sizeRows.querySelectorAll('.size-row');
        chipsWrap.innerHTML = '';
        rows.forEach((row, i) => {
            const label = row.querySelector('input[name*="[label]"]')?.value || `Size ${i + 1}`;
            const price = parseFloat(row.querySelector('input[name*="[price]"]')?.value) || 0;
            const chip = document.createElement('div');
            chip.className = 'size-chip';
            chip.innerHTML = `<span class="size-label">${escHtml(label)}</span><span class="size-chip-price">$${price.toFixed(2)}</span>`;
            chipsWrap.appendChild(chip);
        });
    }

    /* ═══════════════════════════════════════════════════════════
       3. INGREDIENT SEARCH & ADD ROWS
    ══════════════════════════════════════════════════════════════ */
    const ingSearch = document.getElementById('ingredientSearch');
    const ingDropdown = document.getElementById('ingredientDropdown');
    const selectedIngs = document.getElementById('selectedIngredients');
    const ingTemplate = document.getElementById('ingredientRowTemplate');

    // Track which ingredient IDs are already added
    const addedIngIds = new Set(
        (window.EXISTING_INGREDIENT_IDS || []).map(Number)
    );

    // Mark already-added ingredients as disabled in dropdown
    if (ingDropdown) {
        ingDropdown.querySelectorAll('.ing-opt').forEach(opt => {
            if (addedIngIds.has(Number(opt.dataset.id))) opt.classList.add('disabled');
        });
    }

    let ingIndex = selectedIngs ? selectedIngs.querySelectorAll('.ing-row').length : 0;

    if (ingSearch && ingDropdown) {
        ingSearch.addEventListener('focus', () => {
            filterDropdown('');
            ingDropdown.classList.add('open');
        });

        ingSearch.addEventListener('input', () => filterDropdown(ingSearch.value));

        document.addEventListener('click', e => {
            if (!ingSearch.contains(e.target) && !ingDropdown.contains(e.target)) {
                ingDropdown.classList.remove('open');
            }
        });

        // Select ingredient from dropdown
        ingDropdown.addEventListener('click', e => {
            const opt = e.target.closest('.ing-opt');
            if (!opt || opt.classList.contains('disabled')) return;
            addIngredientRow(opt.dataset.id, opt.dataset.name, opt.dataset.unit);
            opt.classList.add('disabled');
            ingSearch.value = '';
            filterDropdown('');
            ingDropdown.classList.remove('open');
        });
    }

    function filterDropdown(query) {
        const q = query.toLowerCase().trim();
        ingDropdown.querySelectorAll('.ing-opt').forEach(opt => {
            const match = opt.dataset.name.toLowerCase().includes(q);
            opt.style.display = match ? '' : 'none';
        });
        ingDropdown.classList.add('open');
    }

    function addIngredientRow(id, name, unit) {
        if (!ingTemplate || !selectedIngs) return;
        const html = ingTemplate.innerHTML
            .replace(/__IDX__/g, ingIndex)
            .replace(/__ID__/g, id)
            .replace(/__NAME__/g, escHtml(name))
            .replace(/__UNIT__/g, escHtml(unit));

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        const row = wrapper.firstChild;

        // Remove button
        row.querySelector('.remove-ing-btn').addEventListener('click', () => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(-6px)';
            row.style.transition = 'all 0.18s ease';
            setTimeout(() => {
                row.remove();
                addedIngIds.delete(Number(id));
                // Re-enable in dropdown
                const opt = ingDropdown?.querySelector(`.ing-opt[data-id="${id}"]`);
                if (opt) opt.classList.remove('disabled');
            }, 180);
        });

        selectedIngs.appendChild(row);
        addedIngIds.add(Number(id));
        ingIndex++;
    }

    // Remove button for pre-rendered edit rows
    if (selectedIngs) {
        selectedIngs.querySelectorAll('.remove-ing-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const row = this.closest('.ing-row');
                const id = row.dataset.ingId;
                row.style.opacity = '0';
                row.style.transform = 'translateY(-6px)';
                row.style.transition = 'all 0.18s ease';
                setTimeout(() => {
                    row.remove();
                    if (id) {
                        addedIngIds.delete(Number(id));
                        const opt = ingDropdown?.querySelector(`.ing-opt[data-id="${id}"]`);
                        if (opt) opt.classList.remove('disabled');
                    }
                }, 180);
            });
        });
    }

    /* ═══════════════════════════════════════════════════════════
       4. FORM SUBMIT — loading state
    ══════════════════════════════════════════════════════════════ */
    const form = document.getElementById('menuItemForm');
    const submitBtn = form?.querySelector('[type="submit"]');

    if (form && submitBtn) {
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
        <svg class="spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
        Saving…`;

            // Spinner CSS
            const style = document.createElement('style');
            style.textContent = `.spin { animation: spin 0.8s linear infinite; } @keyframes spin { to { transform: rotate(360deg); } }`;
            document.head.appendChild(style);
        });
    }

    /* ── Utility ─────────────────────────────────────────────── */
    function escHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str || ''));
        return d.innerHTML;
    }

})();
