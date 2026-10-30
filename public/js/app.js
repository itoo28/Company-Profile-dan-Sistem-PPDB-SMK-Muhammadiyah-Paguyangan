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

  // Auto-Slider Sorotan Berita & Prestasi (Hero Section)
  const heroSliderCard = document.querySelector('.hero-slider-card');
  if (heroSliderCard) {
    const slides = heroSliderCard.querySelectorAll('.hero-slide');
    const dots = heroSliderCard.querySelectorAll('.hero-slider-dot');
    const prevBtn = document.getElementById('heroPrevBtn');
    const nextBtn = document.getElementById('heroNextBtn');
    const currentCounter = document.getElementById('heroCurrentSlide');
    const progressBar = document.getElementById('heroProgressBar');

    if (slides.length > 1) {
      let currentIndex = 0;
      const intervalDuration = 4500; // Bergeser setiap 4.5 detik
      const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      let startTime = Date.now();
      let isPaused = prefersReducedMotion; // Jangan putar otomatis jika pengguna memilih reduced motion (WCAG 2.2.2)

      function goToSlide(index) {
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        currentIndex = index;

        slides.forEach((slide, i) => {
          const isActive = i === currentIndex;
          slide.classList.toggle('is-active', isActive);
          slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');

          // Menjaga aksesibilitas fokus keyboard (WCAG AA & R-32)
          const focusableElements = slide.querySelectorAll('a, button, input');
          focusableElements.forEach(el => {
            if (isActive) {
              el.removeAttribute('tabindex');
            } else {
              el.setAttribute('tabindex', '-1');
            }
          });
        });

        dots.forEach((dot, i) => {
          const isActive = i === currentIndex;
          dot.classList.toggle('is-active', isActive);
          dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        if (currentCounter) {
          currentCounter.textContent = currentIndex + 1;
        }

        startTime = Date.now();
        if (progressBar) {
          progressBar.style.width = '0%';
        }
      }

      function step() {
        if (!isPaused && !prefersReducedMotion) {
          const elapsed = Date.now() - startTime;
          const progress = Math.min((elapsed / intervalDuration) * 100, 100);
          if (progressBar) {
            progressBar.style.width = progress + '%';
          }
          if (elapsed >= intervalDuration) {
            goToSlide(currentIndex + 1);
          }
        }
        requestAnimationFrame(step);
      }

      if (prevBtn) {
        prevBtn.addEventListener('click', function () {
          goToSlide(currentIndex - 1);
        });
      }

      if (nextBtn) {
        nextBtn.addEventListener('click', function () {
          goToSlide(currentIndex + 1);
        });
      }

      dots.forEach((dot, i) => {
        dot.addEventListener('click', function () {
          goToSlide(i);
        });
      });

      // Pause otomatis saat kursor di atas kartu agar pembaca dapat membaca detail
      heroSliderCard.addEventListener('mouseenter', function () {
        isPaused = true;
      });
      heroSliderCard.addEventListener('mouseleave', function () {
        isPaused = prefersReducedMotion;
        startTime = Date.now() - (parseFloat(progressBar ? progressBar.style.width : 0) / 100) * intervalDuration;
      });

      // Pause saat fokus keyboard (WCAG & R-32)
      heroSliderCard.addEventListener('focusin', function () {
        isPaused = true;
      });
      heroSliderCard.addEventListener('focusout', function () {
        isPaused = prefersReducedMotion;
        startTime = Date.now();
      });

      // Pause saat tab browser tidak aktif untuk menghemat daya
      document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
          isPaused = true;
        } else {
          isPaused = prefersReducedMotion;
          startTime = Date.now();
        }
      });

      // Navigasi keyboard tombol panah kiri / kanan
      heroSliderCard.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') {
          goToSlide(currentIndex - 1);
        } else if (e.key === 'ArrowRight') {
          goToSlide(currentIndex + 1);
        }
      });

      // Inisialisasi awal tabindex slide
      goToSlide(0);
      startTime = Date.now();
      requestAnimationFrame(step);
    }
  }

  // Engine Scroll-Reveal Bertingkat (IntersectionObserver - Poin 2.1)
  function initScrollReveal() {
    const revealElements = document.querySelectorAll('.reveal-on-scroll');
    if (!revealElements.length) return;

    // Menghormati preferensi prefers-reduced-motion pengguna (Aksesibilitas WCAG)
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
      revealElements.forEach(el => el.classList.add('is-revealed'));
      return;
    }

    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-revealed');
          observer.unobserve(entry.target);
        }
      });
    }, {
      root: null,
      rootMargin: '0px 0px -40px 0px',
      threshold: 0.1
    });

    revealElements.forEach(el => revealObserver.observe(el));
  }
  initScrollReveal();

  // Filter Interaktif Konsentrasi Keahlian / Jurusan (Poin 3.1)
  function initProgramFilter() {
    const filterButtons = document.querySelectorAll('.program-filter-btn');
    const programCards = document.querySelectorAll('.program-card[data-category]');
    const programGrid = document.querySelector('.program-grid');

    if (!filterButtons.length || !programCards.length) return;

    filterButtons.forEach((btn, index) => {
      btn.addEventListener('click', function () {
        const selectedCategory = btn.getAttribute('data-filter');

        // Update status aktif tombol & aksesibilitas tab (WCAG AA & R-32)
        filterButtons.forEach(b => {
          b.classList.remove('is-active');
          b.setAttribute('aria-selected', 'false');
          b.setAttribute('tabindex', '-1');
        });
        btn.classList.add('is-active');
        btn.setAttribute('aria-selected', 'true');
        btn.removeAttribute('tabindex');

        if (programGrid) {
          programGrid.setAttribute('data-active-filter', selectedCategory);
        }

        // Jalankan animasi transisi masuk halus tanpa merusak inline transform hover
        programCards.forEach(card => {
          const cardCategory = card.getAttribute('data-category');
          const isMatch = selectedCategory === 'all' || cardCategory === selectedCategory;

          card.classList.remove('is-filtering');
          if (isMatch) {
            void card.offsetWidth; // Trigger reflow agar animasi selalu re-trigger
            card.classList.add('is-filtering');
          }
        });
      });

      // Navigasi keyboard tombol panah kiri / kanan / atas / bawah untuk WAI-ARIA tablist
      btn.addEventListener('keydown', function (e) {
        let targetBtn = null;
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
          targetBtn = filterButtons[(index + 1) % filterButtons.length];
        } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
          targetBtn = filterButtons[(index - 1 + filterButtons.length) % filterButtons.length];
        }
        if (targetBtn) {
          e.preventDefault();
          targetBtn.focus();
          targetBtn.click();
        }
      });
    });
  }
  initProgramFilter();
});
