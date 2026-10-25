/**
 * theme-ui.js — UI Interactivity & Custom Theme Dialogs
 * SMK Muhammadiyah Paguyangan (Sesuai DESIGN.md)
 * 
 * Menggantikan elemen bawaan web seperti window.alert(), window.confirm(),
 * alert standar, serta menyelaraskan scrollbar dan file input dengan tema resmi:
 * Deep Royal Navy (#092347), Royal Sapphire Blue (#0b57d0), dan Golden Amber (#f59e0b).
 */

(function () {
  'use strict';

  // --- 1. MODAL DIALOG KONFIRMASI & ALERT TEMA SMK ---
  let modalContainer = null;

  function ensureModalContainer() {
    if (modalContainer && document.body.contains(modalContainer)) {
      return modalContainer;
    }

    modalContainer = document.createElement('div');
    modalContainer.id = 'smkModalBackdrop';
    modalContainer.className = 'smk-modal-backdrop';
    modalContainer.setAttribute('role', 'dialog');
    modalContainer.setAttribute('aria-modal', 'true');
    modalContainer.setAttribute('tabindex', '-1');

    modalContainer.innerHTML = `
      <div class="smk-modal-card" id="smkModalCard">
        <div class="smk-modal-header">
          <div class="smk-modal-icon-badge" id="smkModalIcon"></div>
          <div class="smk-modal-headings">
            <h3 class="smk-modal-title" id="smkModalTitle">Konfirmasi</h3>
            <p class="smk-modal-subtitle" id="smkModalSubtitle">Sistem PPDB & Administrasi SMK Muhammadiyah Paguyangan</p>
          </div>
          <button type="button" class="smk-modal-close" id="smkModalCloseBtn" aria-label="Tutup">&times;</button>
        </div>
        <div class="smk-modal-body" id="smkModalBody"></div>
        <div class="smk-modal-footer" id="smkModalFooter">
          <button type="button" class="smk-modal-btn smk-modal-btn-cancel" id="smkModalCancelBtn">Batal</button>
          <button type="button" class="smk-modal-btn smk-modal-btn-confirm" id="smkModalConfirmBtn">Lanjutkan</button>
        </div>
      </div>
    `;

    document.body.appendChild(modalContainer);

    // Event listener tutup saat klik backdrop
    modalContainer.addEventListener('click', function (e) {
      if (e.target === modalContainer) {
        closeModal(false);
      }
    });

    // Event listener tombol close (X)
    const closeBtn = modalContainer.querySelector('#smkModalCloseBtn');
    if (closeBtn) {
      closeBtn.addEventListener('click', function () {
        closeModal(false);
      });
    }

    // Keyboard navigation (Escape untuk tutup)
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modalContainer && modalContainer.classList.contains('is-open')) {
        closeModal(false);
      }
    });

    return modalContainer;
  }

  let currentResolve = null;

  function closeModal(result) {
    if (!modalContainer) return;
    modalContainer.classList.remove('is-open');
    document.body.style.overflow = '';
    if (currentResolve) {
      currentResolve(result);
      currentResolve = null;
    }
  }

  const ICONS = {
    danger: `
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
        <line x1="12" y1="9" x2="12" y2="13"></line>
        <line x1="12" y1="17" x2="12.01" y2="17"></line>
      </svg>
    `,
    warning: `
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
    `,
    info: `
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="16" x2="12" y2="12"></line>
        <line x1="12" y1="8" x2="12.01" y2="8"></line>
      </svg>
    `,
    success: `
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
        <polyline points="22 4 12 14.01 9 11.01"></polyline>
      </svg>
    `
  };

  /**
   * Dialog Konfirmasi Kustom Tema SMK Muhammadiyah Paguyangan
   * @param {Object} options
   * @returns {Promise<boolean>}
   */
  window.smkConfirm = function (options) {
    if (typeof options === 'string') {
      options = { message: options };
    }

    const opts = Object.assign({
      title: 'Konfirmasi Tindakan',
      message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
      confirmText: 'Ya, Lanjutkan',
      cancelText: 'Batal',
      type: 'warning', // 'danger' | 'warning' | 'info' | 'success'
    }, options);

    // Deteksi tipe otomatis dari isi pesan jika tidak eksplisit
    const lowerMsg = (opts.message + ' ' + opts.title).toLowerCase();
    if (!options.type) {
      if (lowerMsg.includes('hapus') || lowerMsg.includes('delete') || lowerMsg.includes('destroy') || lowerMsg.includes('peringatan') || lowerMsg.includes('batalkan')) {
        opts.type = 'danger';
        if (opts.confirmText === 'Ya, Lanjutkan') {
          opts.confirmText = lowerMsg.includes('batalkan') ? 'Ya, Batalkan' : 'Ya, Hapus Data';
        }
      } else if (lowerMsg.includes('catat') || lowerMsg.includes('seragam') || lowerMsg.includes('nonaktifkan')) {
        opts.type = 'warning';
      }
    }

    return new Promise(function (resolve) {
      ensureModalContainer();
      currentResolve = resolve;

      const titleEl = modalContainer.querySelector('#smkModalTitle');
      const bodyEl = modalContainer.querySelector('#smkModalBody');
      const iconEl = modalContainer.querySelector('#smkModalIcon');
      const confirmBtn = modalContainer.querySelector('#smkModalConfirmBtn');
      const cancelBtn = modalContainer.querySelector('#smkModalCancelBtn');

      titleEl.textContent = opts.title;
      bodyEl.textContent = opts.message;
      iconEl.className = 'smk-modal-icon-badge smk-modal-icon-' + opts.type;
      iconEl.innerHTML = ICONS[opts.type] || ICONS.warning;

      confirmBtn.textContent = opts.confirmText;
      confirmBtn.className = 'smk-modal-btn smk-modal-btn-confirm smk-btn-' + (opts.type === 'danger' ? 'danger' : 'primary');

      cancelBtn.textContent = opts.cancelText;
      cancelBtn.style.display = 'inline-flex';

      // Pasang handler tombol
      confirmBtn.onclick = function () {
        closeModal(true);
      };

      cancelBtn.onclick = function () {
        closeModal(false);
      };

      // Tampilkan modal
      document.body.style.overflow = 'hidden';
      modalContainer.classList.add('is-open');

      // Autofokus tombol konfirmasi
      setTimeout(function () {
        confirmBtn.focus();
      }, 100);
    });
  };

  /**
   * Dialog Alert Kustom Tema SMK Muhammadiyah Paguyangan
   * @param {Object} options
   * @returns {Promise<void>}
   */
  window.smkAlert = function (options) {
    if (typeof options === 'string') {
      options = { message: options };
    }

    const opts = Object.assign({
      title: 'Pemberitahuan',
      message: '',
      confirmText: 'Mengerti',
      type: 'info'
    }, options);

    return new Promise(function (resolve) {
      ensureModalContainer();
      currentResolve = resolve;

      const titleEl = modalContainer.querySelector('#smkModalTitle');
      const bodyEl = modalContainer.querySelector('#smkModalBody');
      const iconEl = modalContainer.querySelector('#smkModalIcon');
      const confirmBtn = modalContainer.querySelector('#smkModalConfirmBtn');
      const cancelBtn = modalContainer.querySelector('#smkModalCancelBtn');

      titleEl.textContent = opts.title;
      bodyEl.textContent = opts.message;
      iconEl.className = 'smk-modal-icon-badge smk-modal-icon-' + opts.type;
      iconEl.innerHTML = ICONS[opts.type] || ICONS.info;

      confirmBtn.textContent = opts.confirmText;
      confirmBtn.className = 'smk-modal-btn smk-modal-btn-confirm smk-btn-primary';

      cancelBtn.style.display = 'none'; // Sembunyikan tombol batal untuk alert tunggal

      confirmBtn.onclick = function () {
        closeModal(true);
      };

      document.body.style.overflow = 'hidden';
      modalContainer.classList.add('is-open');

      setTimeout(function () {
        confirmBtn.focus();
      }, 100);
    });
  };

  // --- 2. FLOATING TOAST NOTIFICATION SYSTEM ---
  let toastContainer = null;

  function ensureToastContainer() {
    if (toastContainer && document.body.contains(toastContainer)) {
      return toastContainer;
    }

    toastContainer = document.createElement('div');
    toastContainer.id = 'smkToastContainer';
    toastContainer.className = 'smk-toast-container';
    toastContainer.setAttribute('aria-live', 'polite');
    toastContainer.setAttribute('aria-atomic', 'true');

    document.body.appendChild(toastContainer);
    return toastContainer;
  }

  /**
   * Menampilkan Toast Notifikasi Elegan
   * @param {Object} options
   */
  window.smkToast = function (options) {
    if (typeof options === 'string') {
      options = { message: options };
    }

    const opts = Object.assign({
      type: 'success', // 'success' | 'error' | 'warning' | 'info'
      title: options.type === 'error' ? 'Pemberitahuan Gagal' : 'Berhasil',
      message: '',
      duration: 5000 // 5 detik
    }, options);

    ensureToastContainer();

    const toast = document.createElement('div');
    toast.className = 'smk-toast smk-toast-' + opts.type;
    toast.setAttribute('role', 'alert');

    toast.innerHTML = `
      <div class="smk-toast-icon">
        ${ICONS[opts.type] || ICONS.info}
      </div>
      <div class="smk-toast-content">
        <div class="smk-toast-title">${opts.title}</div>
        <div class="smk-toast-message">${opts.message}</div>
      </div>
      <button type="button" class="smk-toast-close" aria-label="Tutup">&times;</button>
    `;

    // Pasang tombol close
    const closeBtn = toast.querySelector('.smk-toast-close');
    closeBtn.addEventListener('click', function () {
      dismissToast(toast);
    });

    toastContainer.appendChild(toast);

    // Animasi masuk
    requestAnimationFrame(function () {
      toast.classList.add('is-visible');
    });

    // Auto dismiss timer
    let timer = setTimeout(function () {
      dismissToast(toast);
    }, opts.duration);

    // Pause on hover
    toast.addEventListener('mouseenter', function () {
      clearTimeout(timer);
    });

    toast.addEventListener('mouseleave', function () {
      timer = setTimeout(function () {
        dismissToast(toast);
      }, 2500);
    });
  };

  function dismissToast(toast) {
    if (!toast) return;
    toast.classList.remove('is-visible');
    toast.classList.add('is-hiding');
    setTimeout(function () {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }, 300);
  }

  // --- 3. AUTO-UPGRADE & INTERCEPT FORMS DENGAN CONFIRM ---
  function initAutoInterceptors() {
    // 3.1. Intercept Form dengan onsubmit="return confirm('...')" atau data-confirm
    document.querySelectorAll('form').forEach(function (form) {
      const onsubmitAttr = form.getAttribute('onsubmit');
      if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
        // Ekstrak pesan dari confirm('...')
        const match = onsubmitAttr.match(/confirm\(\s*['"](.*?)['"]\s*\)/s);
        if (match && match[1]) {
          const rawMessage = match[1]
            .replace(/&quot;/g, '"')
            .replace(/&#64;/g, '@')
            .replace(/\\'/g, "'");

          form.dataset.smkConfirm = rawMessage;
          form.removeAttribute('onsubmit'); // Lepas inline confirm bawaan web
        }
      }
    });

    // 3.2. Intercept Button dengan onclick="return confirm('...')"
    document.querySelectorAll('button, a').forEach(function (btn) {
      const onclickAttr = btn.getAttribute('onclick');
      if (onclickAttr && onclickAttr.includes('confirm(')) {
        const match = onclickAttr.match(/confirm\(\s*['"](.*?)['"]\s*\)/s);
        if (match && match[1]) {
          const rawMessage = match[1]
            .replace(/&quot;/g, '"')
            .replace(/&#64;/g, '@')
            .replace(/\\'/g, "'");

          btn.dataset.smkConfirm = rawMessage;
          btn.removeAttribute('onclick');
        }
      }
    });

    // 3.3. Event delegation untuk form submit dengan data-smk-confirm
    document.addEventListener('submit', function (e) {
      const form = e.target;
      if (form && form.dataset && form.dataset.smkConfirm && !form.dataset.smkApproved) {
        e.preventDefault();
        e.stopPropagation();

        const message = form.dataset.smkConfirm;
        const isDelete = message.toLowerCase().includes('hapus') || message.toLowerCase().includes('delete') || message.toLowerCase().includes('peringatan') || message.toLowerCase().includes('batalkan');

        window.smkConfirm({
          title: isDelete ? 'Konfirmasi Hapus Data' : 'Konfirmasi Tindakan',
          message: message,
          type: isDelete ? 'danger' : 'warning',
          confirmText: isDelete ? (message.toLowerCase().includes('batalkan') ? 'Ya, Batalkan' : 'Ya, Hapus Data') : 'Ya, Lanjutkan'
        }).then(function (confirmed) {
          if (confirmed) {
            form.dataset.smkApproved = 'true';
            form.submit();
          }
        });
      }
    }, true);

    // 3.4. Event delegation untuk button click dengan data-smk-confirm
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('[data-smk-confirm]');
      if (btn && !btn.dataset.smkApproved) {
        const form = btn.closest('form');
        const message = btn.dataset.smkConfirm;
        const isDelete = message.toLowerCase().includes('hapus') || message.toLowerCase().includes('delete') || message.toLowerCase().includes('peringatan') || message.toLowerCase().includes('batalkan');

        e.preventDefault();
        e.stopPropagation();

        window.smkConfirm({
          title: isDelete ? 'Konfirmasi Hapus Data' : 'Konfirmasi Tindakan',
          message: message,
          type: isDelete ? 'danger' : 'warning',
          confirmText: isDelete ? 'Ya, Hapus' : 'Ya, Lanjutkan'
        }).then(function (confirmed) {
          if (confirmed) {
            btn.dataset.smkApproved = 'true';
            if (form) {
              window.smkShowLoading(isDelete ? 'Menghapus data...' : 'Memproses tindakan...');
              form.submit();
            } else if (btn.tagName === 'A' && btn.href) {
              window.smkShowLoading('Memuat halaman...');
              window.location.href = btn.href;
            } else {
              btn.click();
            }
          }
        });
      }
    }, true);

    // 3.5. Penyelarasan Tombol Tutup pada Alert Bawaan (.admin-alert, .alert)
    document.querySelectorAll('.admin-alert, .alert, .smk-alert').forEach(function (alertEl) {
      if (!alertEl.querySelector('.smk-alert-close')) {
        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'smk-alert-close';
        closeBtn.setAttribute('aria-label', 'Tutup pemberitahuan');
        closeBtn.innerHTML = '&times;';

        closeBtn.addEventListener('click', function () {
          alertEl.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
          alertEl.style.opacity = '0';
          alertEl.style.transform = 'translateY(-6px)';
          setTimeout(function () {
            if (alertEl.parentNode) {
              alertEl.parentNode.removeChild(alertEl);
            }
          }, 260);
        });

        alertEl.appendChild(closeBtn);
      }
    });

    // 3.6. Otomatis tampilkan floating toast jika ada flash alert saat halaman dimuat
    document.querySelectorAll('.admin-alert, .alert, .smk-alert').forEach(function (alertEl) {
      if (alertEl.dataset.smkToastHandled) return;
      alertEl.dataset.smkToastHandled = 'true';

      const isSuccess = alertEl.classList.contains('smk-alert-success') || alertEl.classList.contains('admin-alert-success') || alertEl.classList.contains('alert-success');
      const isDanger = alertEl.classList.contains('smk-alert-danger') || alertEl.classList.contains('admin-alert-danger') || alertEl.classList.contains('alert-danger');
      const isWarning = alertEl.classList.contains('smk-alert-warning') || alertEl.classList.contains('admin-alert-warning') || alertEl.classList.contains('alert-warning');
      
      const cleanText = alertEl.textContent.replace('×', '').trim();
      if (cleanText) {
        window.smkToast({
          type: isSuccess ? 'success' : (isDanger ? 'error' : (isWarning ? 'warning' : 'info')),
          title: isSuccess ? 'Operasi Berhasil' : (isDanger ? 'Perhatian / Terjadi Kendala' : 'Informasi'),
          message: cleanText,
          duration: 5000
        });
      }
    });

    // 3.7. Inisialisasi Interceptor Navigasi Pindah Halaman & Loading
    setupNavigationInterceptors();
  }

  // --- 4. GLOBAL BRANDED LOADING SPINNER (LOGO SEKOLAH DI TENGAH) ---
  let loadingOverlay = null;
  let loadingTextEl = null;
  let loadingSafetyTimeout = null;

  const LOADING_SPINNER_CSS = `
    .smk-loading-overlay {
      position: fixed;
      inset: 0;
      z-index: 99999999;
      background: rgba(9, 35, 71, 0.72);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transition: opacity 0.22s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.22s ease;
    }

    .smk-loading-overlay.is-active {
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
    }

    .smk-loading-box {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 1.5rem 2rem;
      text-align: center;
      transform: scale(0.92);
      transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
      user-select: none;
    }

    .smk-loading-overlay.is-active .smk-loading-box {
      transform: scale(1);
    }

    .smk-loading-spinner-wrap {
      position: relative;
      width: 96px;
      height: 96px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .smk-spinner-halo {
      position: absolute;
      inset: -6px;
      border-radius: 50%;
      border: 2px dashed rgba(245, 158, 11, 0.4);
      animation: smkSpinnerCounterRotate 8s linear infinite;
    }

    .smk-spinner-ring {
      position: absolute;
      inset: 0;
      border-radius: 50%;
      border: 4px solid rgba(255, 255, 255, 0.12);
      border-top-color: #f59e0b; /* Golden Amber */
      border-right-color: #38bdf8; /* Cyan Sky Blue */
      border-bottom-color: #0b57d0; /* Royal Sapphire Blue */
      border-left-color: rgba(255, 255, 255, 0.2);
      box-shadow: 0 0 24px rgba(11, 87, 208, 0.45), inset 0 0 12px rgba(245, 158, 11, 0.25);
      animation: smkSpinnerRotate 1.05s cubic-bezier(0.55, 0.15, 0.45, 0.85) infinite;
    }

    .smk-spinner-logo-plate {
      width: 58px;
      height: 58px;
      border-radius: 50%;
      background: #ffffff;
      padding: 3px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
      z-index: 2;
      animation: smkLogoBreath 2s ease-in-out infinite alternate;
      overflow: hidden;
    }

    .smk-spinner-logo-img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 50%;
      display: block;
    }

    .smk-loading-text {
      margin-top: 1.25rem;
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      font-size: 0.9375rem;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: 0.02em;
      text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
      max-width: 320px;
      line-height: 1.4;
    }

    .smk-loading-subtext {
      margin-top: 0.35rem;
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      font-size: 0.75rem;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.72);
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    @keyframes smkSpinnerRotate {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    @keyframes smkSpinnerCounterRotate {
      0% { transform: rotate(360deg); }
      100% { transform: rotate(0deg); }
    }

    @keyframes smkLogoBreath {
      0% {
        transform: scale(0.95);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
      }
      100% {
        transform: scale(1.05);
        box-shadow: 0 6px 18px rgba(245, 158, 11, 0.45);
      }
    }
  `;

  function injectLoadingSpinnerStyles() {
    if (document.getElementById('smkLoadingSpinnerStyles')) return;
    const styleEl = document.createElement('style');
    styleEl.id = 'smkLoadingSpinnerStyles';
    styleEl.textContent = LOADING_SPINNER_CSS;
    document.head.appendChild(styleEl);
  }

  function getSchoolLogoUrl() {
    const favicon = document.querySelector('link[rel="icon"], link[rel="shortcut icon"]');
    if (favicon && favicon.href) {
      return favicon.href;
    }
    return '/images/logo.jpg';
  }

  function ensureLoadingOverlay() {
    injectLoadingSpinnerStyles();

    if (loadingOverlay && document.body.contains(loadingOverlay)) {
      return loadingOverlay;
    }

    loadingOverlay = document.createElement('div');
    loadingOverlay.id = 'smkPageLoadingOverlay';
    loadingOverlay.className = 'smk-loading-overlay';
    loadingOverlay.setAttribute('aria-hidden', 'true');
    loadingOverlay.setAttribute('role', 'status');

    const logoUrl = getSchoolLogoUrl();

    loadingOverlay.innerHTML = `
      <div class="smk-loading-box">
        <div class="smk-loading-spinner-wrap">
          <div class="smk-spinner-halo"></div>
          <div class="smk-spinner-ring"></div>
          <div class="smk-spinner-logo-plate">
            <img src="${logoUrl}" alt="Logo SMK Muhammadiyah Paguyangan" class="smk-spinner-logo-img">
          </div>
        </div>
        <div class="smk-loading-text" id="smkLoadingText">Memuat halaman...</div>
        <div class="smk-loading-subtext">SMK Muhammadiyah Paguyangan</div>
      </div>
    `;

    document.body.appendChild(loadingOverlay);
    loadingTextEl = loadingOverlay.querySelector('#smkLoadingText');
    return loadingOverlay;
  }

  window.smkShowLoading = function (message) {
    ensureLoadingOverlay();
    if (loadingTextEl) {
      loadingTextEl.textContent = message || 'Memuat halaman...';
    }
    loadingOverlay.classList.add('is-active');
    loadingOverlay.setAttribute('aria-hidden', 'false');

    // Safety timeout: jika dalam 10 detik halaman belum berpindah, sembunyikan spinner otomatis
    clearTimeout(loadingSafetyTimeout);
    loadingSafetyTimeout = setTimeout(function () {
      window.smkHideLoading();
    }, 10000);
  };

  window.smkHideLoading = function () {
    clearTimeout(loadingSafetyTimeout);
    if (loadingOverlay) {
      loadingOverlay.classList.remove('is-active');
      loadingOverlay.setAttribute('aria-hidden', 'true');
    }
  };

  window.smkIsLoading = function () {
    return loadingOverlay && loadingOverlay.classList.contains('is-active');
  };

  // --- 5. INTERSEPSI NAVIGASI HALAMAN & AKSI FORM ---
  function setupNavigationInterceptors() {
    // 1. Pindah Halaman via Klik Tautan <a>
    document.addEventListener('click', function (e) {
      const link = e.target.closest('a');
      if (!link) return;

      const href = link.getAttribute('href');
      if (!href) return;

      // Abaikan hash atau tautan kosong
      if (href === '#' || href.startsWith('#')) return;

      // Abaikan skrip javascript: atau protokol eksternal
      if (href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

      // Abaikan jika membuka di tab baru (target="_blank")
      if (link.target && link.target.toLowerCase() === '_blank') return;

      // Abaikan download dokumen explisit
      if (link.hasAttribute('download')) return;

      // Abaikan jika tautan ditandai bebas loading
      if (link.dataset.noLoading !== undefined || link.dataset.noSpinner !== undefined) return;

      // Abaikan jika ditekan dengan tombol modifikasi (Ctrl, Cmd, Shift, Alt) atau klik kanan/tengah
      if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) return;

      try {
        const targetUrl = new URL(link.href, window.location.href);
        const currentUrl = new URL(window.location.href);

        // Jika anchor / hash di halaman yang sama persis, jangan tampilkan spinner
        if (targetUrl.origin === currentUrl.origin &&
            targetUrl.pathname === currentUrl.pathname &&
            targetUrl.search === currentUrl.search) {
          return;
        }

        // Jika tautan ekspor atau file dokumen (PDF/Excel/CSV), tampilkan sesaat lalu auto-dismiss
        if (targetUrl.pathname.includes('/ekspor/') || targetUrl.pathname.endsWith('.pdf') || targetUrl.pathname.endsWith('.csv') || targetUrl.pathname.endsWith('.xlsx')) {
          window.smkShowLoading('Menyiapkan dokumen unduhan...');
          setTimeout(function () {
            window.smkHideLoading();
          }, 3000);
          return;
        }

        window.smkShowLoading('Memuat halaman...');
      } catch (err) {
        // Fallback jika format URL tidak standar
      }
    }, true);

    // 2. Submit Formulir (Aksi yang memiliki proses loading bawaan)
    document.addEventListener('submit', function (e) {
      const form = e.target;
      if (!form) return;

      // Jika form membuka di tab baru (_blank), jangan tutupi layar utama
      if (form.target && form.target.toLowerCase() === '_blank') return;

      // Jika form ditandai bebas loading
      if (form.dataset.noLoading !== undefined || form.dataset.noSpinner !== undefined) return;

      // Jika validasi bawaan HTML5 gagal, jangan tampilkan spinner
      if (form.checkValidity && !form.checkValidity()) {
        return;
      }

      // Jika form masih menunggu konfirmasi modal (data-smk-confirm dan belum approved), tunda spinner
      if (form.dataset.smkConfirm && !form.dataset.smkApproved) {
        return;
      }

      let loadingMsg = 'Memproses data...';
      const action = (form.getAttribute('action') || '').toLowerCase();
      const hasFiles = form.querySelector('input[type="file"]');
      if (hasFiles && Array.from(form.querySelectorAll('input[type="file"]')).some(fi => fi.files && fi.files.length > 0)) {
        loadingMsg = 'Mengunggah berkas & memproses...';
      } else if (action.includes('login') || action.includes('authenticate')) {
        loadingMsg = 'Memverifikasi akun...';
      } else if (action.includes('logout')) {
        loadingMsg = 'Keluar dari sesi...';
      } else if (action.includes('daftar') || action.includes('pendaftar')) {
        loadingMsg = 'Menyimpan data pendaftaran...';
      } else if (action.includes('password')) {
        loadingMsg = 'Memperbarui kata sandi...';
      } else if (action.includes('verifikasi')) {
        loadingMsg = 'Menyimpan hasil verifikasi...';
      } else if (action.includes('bayar') || action.includes('pembayaran')) {
        loadingMsg = 'Memproses pembayaran...';
      }

      window.smkShowLoading(loadingMsg);
    }, true);

    // 3. Tangani kembalinya halaman via cache browser (back/forward bfcache)
    window.addEventListener('pageshow', function () {
      window.smkHideLoading();
    });

    // 4. Integrasi event Livewire / Flux UI
    document.addEventListener('livewire:navigating', function () {
      window.smkShowLoading('Memuat halaman...');
    });
    document.addEventListener('livewire:navigated', function () {
      window.smkHideLoading();
    });

    // 5. Izinkan penutupan darurat dengan tombol Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && window.smkIsLoading()) {
        window.smkHideLoading();
      }
    });
  }

  // Jalankan saat DOM siap
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAutoInterceptors);
  } else {
    initAutoInterceptors();
  }
})();
