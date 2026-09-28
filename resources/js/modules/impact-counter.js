/**
 * impact-counter.js
 * Animates the impact stat counters on the landing page
 * using IntersectionObserver so they run once when visible.
 */

document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('[data-target]');
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            animateCounter(entry.target);
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.3 });

    counters.forEach(el => observer.observe(el));

    /**
     * Count up from 0 to the element's data-target value.
     * @param {HTMLElement} el
     */
    function animateCounter(el) {
        const target   = parseFloat(el.dataset.target) || 0;
        const format   = el.dataset.format;
        const duration = 1800; // ms
        const steps    = 60;
        const increment = target / steps;
        let current    = 0;
        let step       = 0;

        const timer = setInterval(() => {
            step++;
            current = Math.min(current + increment, target);

            el.textContent = format === 'currency'
                ? formatCurrency(Math.round(current))
                : Math.round(current).toLocaleString('id-ID');

            if (step >= steps) {
                clearInterval(timer);
                // Ensure final value is exact
                el.textContent = format === 'currency'
                    ? formatCurrency(target)
                    : Math.round(target).toLocaleString('id-ID');
            }
        }, duration / steps);
    }

    /**
     * Format a number as Indonesian Rupiah abbreviation.
     * @param {number} value
     * @returns {string}
     */
    function formatCurrency(value) {
        if (value >= 1_000_000_000) return 'Rp ' + (value / 1_000_000_000).toFixed(1) + 'M';
        if (value >= 1_000_000)     return 'Rp ' + (value / 1_000_000).toFixed(1) + ' jt';
        if (value >= 1_000)         return 'Rp ' + (value / 1_000).toFixed(0) + ' rb';
        return 'Rp ' + value.toLocaleString('id-ID');
    }
});
