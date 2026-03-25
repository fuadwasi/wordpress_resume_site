/**
 * skills-animation.js
 *
 * Animates skill progress bars when they scroll into view,
 * using the IntersectionObserver API.
 *
 * Also animates counter numbers in the hero stats.
 *
 * @package FuadHasanPortfolio
 */
(function () {
    'use strict';

    /* ----------------------------------------------------------------
       Skill bars — animate width on scroll into view
    ---------------------------------------------------------------- */
    const bars = document.querySelectorAll('.skill-group__bar-fill');

    if ('IntersectionObserver' in window && bars.length) {
        const barObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bar       = entry.target;
                    const targetPct = parseFloat(bar.dataset.width) || 0;
                    bar.style.width = targetPct + '%';
                    barObserver.unobserve(bar);
                }
            });
        }, { threshold: 0.2 });

        bars.forEach(bar => barObserver.observe(bar));
    } else {
        // Fallback for browsers without IntersectionObserver
        bars.forEach(bar => {
            bar.style.width = (parseFloat(bar.dataset.width) || 0) + '%';
        });
    }

    /* ----------------------------------------------------------------
       Hero stat counter animation
    ---------------------------------------------------------------- */
    const counters = document.querySelectorAll('.hero__stat-number[data-count]');

    if ('IntersectionObserver' in window && counters.length) {
        const counterObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(c => counterObserver.observe(c));
    }

    /**
     * Animate a counter element from 0 to its data-count value.
     *
     * @param {HTMLElement} el
     */
    function animateCounter(el) {
        const target   = parseInt(el.dataset.count, 10);
        const suffix   = el.textContent.includes('+') ? '+' : '';
        const duration = 1200;
        const start    = performance.now();

        function step(now) {
            const elapsed  = now - start;
            const progress = Math.min(elapsed / duration, 1);
            // Ease-out cubic
            const eased    = 1 - Math.pow(1 - progress, 3);
            const current  = Math.round(eased * target);
            el.textContent = current + suffix;
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }

        requestAnimationFrame(step);
    }

})();
