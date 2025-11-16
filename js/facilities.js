document.addEventListener('DOMContentLoaded', function() {
    // Initialize intersection observer for animation
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    });

    // Observe all facility cards
    const facilityCards = document.querySelectorAll('#facilities-section .facility-card');
    facilityCards.forEach(card => {
        observer.observe(card);
    });

    // Add animation classes
    facilityCards.forEach((card, index) => {
        card.style.animationDelay = `${0.1 * (index + 1)}s`;
    });
});
