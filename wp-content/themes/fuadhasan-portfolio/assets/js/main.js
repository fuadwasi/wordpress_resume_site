/**
 * main.js — Core theme interactions
 *
 * Responsibilities:
 *   - Header scroll state
 *   - Mobile navigation toggle
 *   - Project type filter
 *   - Animated hero title cycling
 *   - Keyboard navigation for menus
 *
 * @package FuadHasanPortfolio
 */
(function () {
    'use strict';

    /* ----------------------------------------------------------------
       Header: add .is-scrolled class on scroll
    ---------------------------------------------------------------- */
    const header = document.getElementById('site-header');
    if (header) {
        const onScroll = () => {
            header.classList.toggle('is-scrolled', window.scrollY > 20);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    /* ----------------------------------------------------------------
       Mobile navigation toggle
    ---------------------------------------------------------------- */
    const navToggle = document.getElementById('nav-toggle');
    const siteNav   = document.getElementById('site-nav');

    if (navToggle && siteNav) {
        navToggle.addEventListener('click', () => {
            const isOpen = siteNav.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', String(isOpen));
            document.body.style.overflow = isOpen ? 'hidden' : '';
            // Move focus to first nav link when menu opens
            if (isOpen) {
                const firstLink = siteNav.querySelector('a');
                if (firstLink) firstLink.focus();
            }
        });

        // Close nav when a link is clicked
        siteNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                siteNav.classList.remove('is-open');
                navToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            });
        });

        // Close on Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && siteNav.classList.contains('is-open')) {
                siteNav.classList.remove('is-open');
                navToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
                navToggle.focus();
            }
        });
    }

    /* ----------------------------------------------------------------
       Animated hero title cycling
    ---------------------------------------------------------------- */
    const titleEl = document.querySelector('.hero__title-animated');
    if (titleEl) {
        let titles;
        try {
            titles = JSON.parse(titleEl.dataset.titles || '[]');
        } catch (_) {
            titles = [];
        }

        if (titles.length > 1) {
            let idx = 0;
            setInterval(() => {
                titleEl.classList.add('is-fading');
                setTimeout(() => {
                    idx = (idx + 1) % titles.length;
                    titleEl.textContent = titles[idx];
                    titleEl.classList.remove('is-fading');
                }, 250);
            }, 3000);
        }
    }

    /* ----------------------------------------------------------------
       Project type filter
    ---------------------------------------------------------------- */
    const filterBtns = document.querySelectorAll('.projects-filter__btn');
    const projectGrid = document.getElementById('projects-grid');

    if (filterBtns.length && projectGrid) {
        const filterStatus = document.getElementById('projects-filter-status');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.dataset.filter;

                // Update active state and aria-pressed
                filterBtns.forEach(b => {
                    b.classList.remove('is-active');
                    b.setAttribute('aria-pressed', 'false');
                });
                btn.classList.add('is-active');
                btn.setAttribute('aria-pressed', 'true');

                // Show/hide cards
                let visibleCount = 0;
                projectGrid.querySelectorAll('[data-type]').forEach(card => {
                    if (filter === 'all' || card.dataset.type === filter) {
                        card.classList.remove('is-hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('is-hidden');
                    }
                });

                // Announce result count to screen readers
                if (filterStatus) {
                    filterStatus.textContent = visibleCount + (visibleCount === 1 ? ' project shown' : ' projects shown');
                }
            });
        });
    }

    /* ----------------------------------------------------------------
       Animate elements on scroll (IntersectionObserver)
    ---------------------------------------------------------------- */
    const animateEls = document.querySelectorAll('.animate-fade-in-up, .animate-fade-in');

    if ('IntersectionObserver' in window && animateEls.length) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        animateEls.forEach(el => {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });
    }

    /* ----------------------------------------------------------------
       Back to top button
    ---------------------------------------------------------------- */
    const backToTopBtn = document.getElementById('back-to-top');
    if (backToTopBtn) {
        const toggleVisibility = () => {
            if (window.scrollY > 400) {
                backToTopBtn.removeAttribute('hidden');
            } else {
                backToTopBtn.setAttribute('hidden', '');
            }
        };
        window.addEventListener('scroll', toggleVisibility, { passive: true });
        toggleVisibility();

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            // Return focus to skip link for keyboard users
            const skipLink = document.querySelector('.skip-link');
            if (skipLink) skipLink.focus();
        });
    }

})();
