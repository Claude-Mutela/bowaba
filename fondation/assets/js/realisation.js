    // Animation for achievement cards
    const achievementCards = document.querySelectorAll('.achievement-card');
    
    function animateCards() {
        achievementCards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('animated');
            }, 150 * index);
        });
    }
    
    // Trigger animation when section is in view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCards();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    observer.observe(document.querySelector('.achievements'));