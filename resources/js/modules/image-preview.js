/**
 * image-preview.js
 * Live preview of selected images in the donor upload form.
 * - Click or drag-and-drop on the drop zone
 * - Shows thumbnails with a remove button
 * - Enforces max 5 files
 */

document.addEventListener('DOMContentLoaded', () => {
    const dropZone  = document.getElementById('image-drop-zone');
    const fileInput = document.getElementById('image-input');
    const previewEl = document.getElementById('image-previews');

    if (!dropZone || !fileInput || !previewEl) return;

    const MAX_FILES = 5;
    let selectedFiles = []; // DataTransfer workaround for multi-file management

    // ── Click on drop zone opens file picker ──
    dropZone.addEventListener('click', () => fileInput.click());

    // ── Drag and Drop ──
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-teal-400', 'bg-teal-50');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-teal-400', 'bg-teal-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-teal-400', 'bg-teal-50');
        handleFiles(Array.from(e.dataTransfer.files));
    });

    // ── File input change ──
    fileInput.addEventListener('change', () => {
        handleFiles(Array.from(fileInput.files));
    });

    /**
     * Process newly selected files: validate, add to list, render previews.
     * @param {File[]} newFiles
     */
    function handleFiles(newFiles) {
        const imageFiles = newFiles.filter(f => f.type.startsWith('image/'));
        const combined   = [...selectedFiles, ...imageFiles].slice(0, MAX_FILES);
        selectedFiles    = combined;

        renderPreviews();
        syncInput();
    }

    /** Render thumbnail grid from selectedFiles. */
    function renderPreviews() {
        previewEl.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const url  = URL.createObjectURL(file);
            const wrap = document.createElement('div');
            wrap.className = 'relative w-20 h-20 rounded-xl overflow-hidden border border-slate-200 group';

            const img = document.createElement('img');
            img.src       = url;
            img.className = 'w-full h-full object-cover';
            img.onload    = () => URL.revokeObjectURL(url); // free memory

            const removeBtn = document.createElement('button');
            removeBtn.type      = 'button';
            removeBtn.className =
                'absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 text-white text-xs ' +
                'opacity-0 group-hover:opacity-100 transition flex items-center justify-center';
            removeBtn.textContent = '×';
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                selectedFiles.splice(index, 1);
                renderPreviews();
                syncInput();
            });

            // Primary badge
            if (index === 0) {
                const badge = document.createElement('span');
                badge.className = 'absolute bottom-0 left-0 right-0 text-center text-[9px] bg-teal-600 text-white py-0.5';
                badge.textContent = 'Utama';
                wrap.appendChild(badge);
            }

            wrap.appendChild(img);
            wrap.appendChild(removeBtn);
            previewEl.appendChild(wrap);
        });

        // Update instruction text
        const instruction = dropZone.querySelector('p');
        if (instruction) {
            instruction.textContent = selectedFiles.length
                ? `${selectedFiles.length}/${MAX_FILES} foto dipilih`
                : 'Klik atau seret foto ke sini';
        }
    }

    /**
     * Sync the DataTransfer list back to the actual <input type="file">.
     * This keeps server-side FormRequest validation working.
     */
    function syncInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        fileInput.files = dt.files;
    }
});
