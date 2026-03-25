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
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.dataset.filter;

                // Update active state
                filterBtns.forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');

                // Show/hide cards
                projectGrid.querySelectorAll('[data-type]').forEach(card => {
                    if (filter === 'all' || card.dataset.type === filter) {
                        card.classList.remove('is-hidden');
                    } else {
                        card.classList.add('is-hidden');
                    }
                });
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

})();
