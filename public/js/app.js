/**
 * JavaScript Interaktivitas Publik SMK Muhammadiyah Paguyangan
 * Memenuhi standar aksesibilitas keyboard (R-32) dan menu mobile responsif (R-03).
 */

document.addEventListener('DOMContentLoaded', function () {
  const mobileToggle = document.getElementById('mobileMenuToggle');
  const mobileDrawer = document.getElementById('mobileDrawer');
  const siteHeader = document.querySelector('.site-header');

  // Toggle Menu Mobile
  const mobileDrawerClose = document.getElementById('mobileDrawerClose');

  function closeMobileDrawer() {
    if (mobileDrawer && mobileDrawer.classList.contains('is-open')) {
      mobileDrawer.classList.remove('is-open');
      if (mobileToggle) {
        mobileToggle.setAttribute('aria-expanded', 'false');
        mobileToggle.focus();
      }
      document.body.style.overflow = '';
    }
  }

  if (mobileToggle && mobileDrawer) {
    mobileToggle.addEventListener('click', function () {
      const isExpanded = mobileToggle.getAttribute('aria-expanded') === 'true';
      mobileToggle.setAttribute('aria-expanded', !isExpanded);
      mobileDrawer.classList.toggle('is-open');

      if (!isExpanded) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    });

    if (mobileDrawerClose) {
      mobileDrawerClose.addEventListener('click', closeMobileDrawer);
    }

    // Menutup dengan tombol Escape (R-32)
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && mobileDrawer.classList.contains('is-open')) {
        closeMobileDrawer();
      }
    });

    // Tutup jika klik area overlay
    mobileDrawer.addEventListener('click', function (e) {
      if (e.target === mobileDrawer) {
        closeMobileDrawer();
      }
    });
  }

  // Interaktivitas Dropdown Navigasi Desktop (WCAG AA & Touch Screen Support R-03, R-32)
  const dropdownButtons = document.querySelectorAll('.nav-dropdown-btn');
  const dropdownContainers = document.querySelectorAll('.nav-item-dropdown');

  dropdownButtons.forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      const parent = btn.closest('.nav-item-dropdown');
      const isCurrentlyOpen = parent.classList.contains('is-open');

      // Tutup dropdown lain yang sedang terbuka
      dropdownContainers.forEach(container => {
        if (container !== parent) {
          container.classList.remove('is-open');
          const otherBtn = container.querySelector('.nav-dropdown-btn');
          if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
        }
      });

      // Toggle dropdown aktif
      if (isCurrentlyOpen) {
        parent.classList.remove('is-open');
        btn.setAttribute('aria-expanded', 'false');
      } else {
        parent.classList.add('is-open');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  });

  // Tutup dropdown jika klik di luar elemen navbar
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.nav-item-dropdown')) {
      dropdownContainers.forEach(container => {
        container.classList.remove('is-open');
        const btn = container.querySelector('.nav-dropdown-btn');
        if (btn) btn.setAttribute('aria-expanded', 'false');
      });
    }
  });

  // Tutup dropdown saat tombol Escape ditekan
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      dropdownContainers.forEach(container => {
        if (container.classList.contains('is-open')) {
          container.classList.remove('is-open');
          const btn = container.querySelector('.nav-dropdown-btn');
          if (btn) {
            btn.setAttribute('aria-expanded', 'false');
            btn.focus();
          }
        }
      });
    }
  });

  // Efek Sticky Header saat digulir
  if (siteHeader) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 20) {
        siteHeader.classList.add('is-scrolled');
      } else {
        siteHeader.classList.remove('is-scrolled');
      }
    }, { passive: true });
  }
});
