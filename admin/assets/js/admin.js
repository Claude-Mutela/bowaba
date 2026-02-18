/**
 * BowaBanCongo — Admin CMS JavaScript
 * Handles: TinyMCE init, slug generation, image preview, sidebar toggle, interactions
 */

'use strict';

/* ============================================================
   TINYMCE INITIALIZATION
   ============================================================ */

/**
 * Initialize TinyMCE on all elements matching the selector.
 * @param {string} selector - CSS selector for textarea elements
 */
function initTinyMCE(selector = '.tinymce-editor') {
    if (typeof tinymce === 'undefined') {
        console.warn('TinyMCE not loaded.');
        return;
    }

    tinymce.init({
        selector: selector,
        language: 'fr_FR',
        height: 480,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
            'codesample', 'quickbars'
        ],
        toolbar:
            'undo redo | styles | bold italic underline strikethrough | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | link image media table | ' +
            'forecolor backcolor | removeformat | fullscreen code | help',
        toolbar_sticky: true,
        toolbar_sticky_offset: 64, // topbar height
        content_style: `
      body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 14px;
        color: #1a2332;
        line-height: 1.7;
        padding: 16px;
        max-width: 100%;
      }
      h1, h2, h3, h4, h5, h6 {
        font-family: 'Raleway', sans-serif;
        color: #0a1628;
      }
      a { color: #008ff2; }
      img { max-width: 100%; height: auto; border-radius: 6px; }
      blockquote {
        border-left: 4px solid #008ff2;
        padding-left: 16px;
        margin-left: 0;
        color: #6b7a8d;
        font-style: italic;
      }
    `,
        skin: 'oxide',
        icons: 'default',
        branding: false,
        promotion: false,
        resize: true,
        statusbar: true,
        // Image upload handler (adapt to your backend)
        images_upload_url: '../api/upload-image.php',
        images_upload_credentials: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: (cb, value, meta) => {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];
                const reader = new FileReader();
                reader.addEventListener('load', () => {
                    const id = 'blobid' + Date.now();
                    const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    const base64 = reader.result.split(',')[1];
                    const blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    cb(blobInfo.blobUri(), { title: file.name });
                });
                reader.readAsDataURL(file);
            });
            input.click();
        },
        setup: (editor) => {
            editor.on('change', () => {
                editor.save(); // sync with underlying textarea
            });
        }
    });
}

/* ── Compact TinyMCE for short content (description fields) ── */
function initTinyMCECompact(selector = '.tinymce-compact') {
    if (typeof tinymce === 'undefined') return;

    tinymce.init({
        selector: selector,
        language: 'fr_FR',
        height: 280,
        menubar: false,
        plugins: ['lists', 'link', 'autolink', 'wordcount'],
        toolbar: 'bold italic underline | bullist numlist | link | removeformat',
        content_style: `
      body {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: #1a2332;
        line-height: 1.7;
        padding: 12px;
      }
    `,
        branding: false,
        promotion: false,
        statusbar: false,
        setup: (editor) => {
            editor.on('change', () => editor.save());
        }
    });
}

/* ============================================================
   SLUG GENERATION
   ============================================================ */

/**
 * Convert a string to a URL-friendly slug.
 * @param {string} text
 * @returns {string}
 */
function slugify(text) {
    const map = {
        'à': 'a', 'â': 'a', 'ä': 'a', 'á': 'a', 'ã': 'a',
        'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e',
        'ì': 'i', 'î': 'i', 'ï': 'i', 'í': 'i',
        'ò': 'o', 'ô': 'o', 'ö': 'o', 'ó': 'o', 'õ': 'o',
        'ù': 'u', 'û': 'u', 'ü': 'u', 'ú': 'u',
        'ç': 'c', 'ñ': 'n', 'ý': 'y', 'ÿ': 'y',
        'À': 'a', 'Â': 'a', 'Ä': 'a', 'Á': 'a',
        'È': 'e', 'É': 'e', 'Ê': 'e', 'Ë': 'e',
        'Î': 'i', 'Ï': 'i', 'Ó': 'o', 'Ô': 'o',
        'Ù': 'u', 'Û': 'u', 'Ü': 'u', 'Ç': 'c', 'Ñ': 'n'
    };
    return text
        .split('')
        .map(c => map[c] || c)
        .join('')
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

/**
 * Bind title → slug auto-generation.
 * @param {string} titleId  - ID of the title input
 * @param {string} slugId   - ID of the slug input
 */
function bindSlugGeneration(titleId, slugId) {
    const titleEl = document.getElementById(titleId);
    const slugEl = document.getElementById(slugId);
    if (!titleEl || !slugEl) return;

    let userEdited = slugEl.value.trim() !== '';

    titleEl.addEventListener('input', () => {
        if (!userEdited) {
            slugEl.value = slugify(titleEl.value);
        }
    });

    slugEl.addEventListener('input', () => {
        userEdited = slugEl.value.trim() !== '';
    });

    // Sanitize slug on blur
    slugEl.addEventListener('blur', () => {
        slugEl.value = slugify(slugEl.value);
    });
}

/* ============================================================
   IMAGE PREVIEW
   ============================================================ */

/**
 * Bind file input to show image preview.
 * @param {string} inputId    - ID of the file input
 * @param {string} previewId  - ID of the preview container
 * @param {string} zoneId     - ID of the upload zone (to hide)
 */
function bindImagePreview(inputId, previewId, zoneId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const zone = document.getElementById(zoneId);
    if (!input || !preview) return;

    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (ev) => {
            const img = preview.querySelector('img');
            if (img) img.src = ev.target.result;
            preview.style.display = 'block';
            if (zone) zone.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
}

/**
 * Remove image preview and reset file input.
 * @param {string} inputId   - ID of the file input
 * @param {string} previewId - ID of the preview container
 * @param {string} zoneId    - ID of the upload zone
 */
function removeImagePreview(inputId, previewId, zoneId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const zone = document.getElementById(zoneId);

    if (input) input.value = '';
    if (preview) preview.style.display = 'none';
    if (zone) zone.style.display = 'block';
}

/* ============================================================
   SIDEBAR TOGGLE (Mobile)
   ============================================================ */
function initSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const menuBtn = document.getElementById('mobileMenuBtn');

    if (!sidebar) return;

    function openSidebar() {
        sidebar.classList.add('show');
        if (overlay) overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (menuBtn) menuBtn.addEventListener('click', openSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // Close on ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSidebar();
    });
}

/* ============================================================
   CONFIRM DELETE
   ============================================================ */
function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
    return window.confirm(message);
}

/* Attach to all delete buttons */
function initDeleteConfirm() {
    document.querySelectorAll('[data-confirm-delete]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const msg = btn.dataset.confirmDelete || 'Êtes-vous sûr de vouloir supprimer cet élément ?';
            if (!confirmDelete(msg)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
}

/* ============================================================
   AUTO-DISMISS ALERTS
   ============================================================ */
function initAlerts() {
    document.querySelectorAll('.admin-alert[data-auto-dismiss]').forEach(alert => {
        const delay = parseInt(alert.dataset.autoDismiss) || 4000;
        setTimeout(() => {
            alert.style.transition = 'opacity .4s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 400);
        }, delay);
    });
}

/* ============================================================
   DISPLAY ORDER — Inline edit
   ============================================================ */
function initOrderInputs() {
    document.querySelectorAll('.order-input').forEach(input => {
        input.addEventListener('change', function () {
            const row = this.closest('tr');
            if (!row) return;
            row.style.transition = 'background .3s';
            row.style.background = '#e8f4fd';
            setTimeout(() => { row.style.background = ''; }, 800);
        });
    });
}

/* ============================================================
   CHARACTER COUNTER
   ============================================================ */
function initCharCounters() {
    document.querySelectorAll('[data-max-chars]').forEach(el => {
        const max = parseInt(el.dataset.maxChars);
        const counterId = el.dataset.counterId;
        const counter = counterId ? document.getElementById(counterId) : null;

        function update() {
            const len = el.value.length;
            if (counter) {
                counter.textContent = `${len} / ${max}`;
                counter.style.color = len > max * 0.9 ? '#ef4444' : '#6b7a8d';
            }
        }

        el.addEventListener('input', update);
        update();
    });
}

/* ============================================================
   FEATURED TOGGLE (star icon)
   ============================================================ */
function initFeaturedToggle() {
    document.querySelectorAll('.featured-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const star = this.querySelector('i');
            if (!star) return;
            const isFeatured = star.classList.contains('bi-star-fill');
            star.classList.toggle('bi-star-fill', !isFeatured);
            star.classList.toggle('bi-star', isFeatured);
            star.classList.toggle('featured-star', !isFeatured);
            star.classList.toggle('off', isFeatured);

            const hiddenInput = document.getElementById(this.dataset.targetInput);
            if (hiddenInput) hiddenInput.value = isFeatured ? '0' : '1';
        });
    });
}

/* ============================================================
   SEARCH FILTER (client-side table filter)
   ============================================================ */
function initTableSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;

    input.addEventListener('input', () => {
        const query = input.value.toLowerCase().trim();
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
}

/* ============================================================
   INIT ALL
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initDeleteConfirm();
    initAlerts();
    initOrderInputs();
    initCharCounters();
    initFeaturedToggle();

    // TinyMCE — only init if elements exist
    if (document.querySelector('.tinymce-editor')) {
        initTinyMCE('.tinymce-editor');
    }
    if (document.querySelector('.tinymce-compact')) {
        initTinyMCECompact('.tinymce-compact');
    }

    // Slug bindings (article form)
    bindSlugGeneration('articleTitle', 'articleSlug');
    bindSlugGeneration('serviceTitle', 'serviceSlug');
    bindSlugGeneration('categoryName', 'categorySlug');

    // Image preview
    bindImagePreview('coverImageInput', 'coverImagePreview', 'coverImageZone');
    bindImagePreview('serviceImageInput', 'serviceImagePreview', 'serviceImageZone');

    // Table search
    initTableSearch('articleSearch', 'articlesTable');
    initTableSearch('serviceSearch', 'servicesTable');
    initTableSearch('categorySearch', 'categoriesTable');
    initTableSearch('userSearch', 'usersTable');
});
