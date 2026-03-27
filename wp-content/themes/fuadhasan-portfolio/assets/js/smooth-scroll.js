/**
 * smooth-scroll.js — Smooth scrolling for anchor links
 *
 * Intercepts clicks on in-page anchor links and scrolls smoothly,
 * accounting for the fixed header height.
 *
 * @package FuadHasanPortfolio
 */
(function () {
    'use strict';

    const header = document.getElementById('site-header');

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const id = this.getAttribute('href');
            if (!id || id === '#') return;

            const target = document.querySelector(id);
            if (!target) return;

            e.preventDefault();

            const headerH = header ? header.offsetHeight : 0;
            const offset  = target.getBoundingClientRect().top + window.scrollY - headerH - 16;

            window.scrollTo({ top: offset, behavior: 'smooth' });

            // Update URL without scrolling
            if (history.pushState) {
                history.pushState(null, '', id);
            }

            // Move focus for accessibility
            target.setAttribute('tabindex', '-1');
            target.focus({ preventScroll: true });
        });
    });

})();
