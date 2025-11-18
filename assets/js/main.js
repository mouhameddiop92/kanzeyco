// ============================================
// Smooth Scroll Navigation
// ============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// ============================================
// Navbar Scroll Effect
// ============================================
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
    } else {
        navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
    }
});

// ============================================
// Contact Form Submission
// ============================================
document.querySelector('.contact-form-card')?.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Merci pour votre message! Nous vous contacterons bientôt.');
    this.reset();
});

// ============================================
// Animation des chiffres d'impact
// ============================================
function animateImpactNumber(element) {
    const originalText = element.textContent.trim();
    let finalValue = 0;
    let suffix = '';
    let hasK = false;
    
    // Extraire le nombre et le suffixe
    if (originalText.includes('K')) {
        // Gérer les cas comme "20K+"
        const match = originalText.match(/(\d+)K/);
        if (match) {
            finalValue = parseInt(match[1]) * 1000;
            hasK = true;
        }
        suffix = originalText.replace(/\d+K/, ''); // Garder le "+" ou autre
    } else {
        // Gérer les cas normaux comme "500+", "1000+", "50+"
        const match = originalText.match(/(\d+)/);
        if (match) {
            finalValue = parseInt(match[1]);
        }
        suffix = originalText.replace(/\d+/, ''); // Garder le "+" ou autre
    }
    
    if (finalValue === 0) return;
    
    let current = 0;
    const duration = 2000; // 2 secondes
    const steps = 60;
    const increment = finalValue / steps;
    const stepDuration = duration / steps;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= finalValue) {
            // Afficher la valeur finale avec le bon format
            if (hasK) {
                const kValue = finalValue / 1000;
                element.textContent = kValue + 'K' + suffix;
            } else {
                element.textContent = finalValue + suffix;
            }
            clearInterval(timer);
        } else {
            // Afficher la valeur courante
            if (hasK) {
                const kValue = Math.floor(current / 1000);
                element.textContent = kValue + 'K' + suffix;
            } else {
                element.textContent = Math.floor(current) + suffix;
            }
        }
    }, stepDuration);
}

// Observer pour les nouveaux chiffres d'impact
const impactNumberObserver = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const target = entry.target;
            if (target.classList.contains('impact-number-new') && !target.dataset.animated) {
                target.dataset.animated = 'true';
                animateImpactNumber(target);
            }
        }
    });
}, {
    threshold: 0.3,
    rootMargin: '0px'
});

// Observer tous les éléments avec la classe impact-number-new
document.querySelectorAll('.impact-number-new').forEach(number => {
    impactNumberObserver.observe(number);
});

// ============================================
// Animation des sections au scroll
// ============================================
function initScrollAnimations() {
    const sectionObserverOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    };

    const sectionObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Ajouter la classe is-visible pour déclencher les animations
                entry.target.classList.add('is-visible');
                // Ne plus observer cette section une fois l'animation déclenchée
                sectionObserver.unobserve(entry.target);
            }
        });
    }, sectionObserverOptions);

    // Observer les sections avec animations
    const sectionsToAnimate = document.querySelectorAll(
        '#vision-mission, .metiers-section, .transversal-solutions, .values-section, ' +
        '.impact-section, .trust-section, .news-section, .about-section, .why-us-section, ' +
        '.team-section, .partners-section, .testimonials-section, .contact-section'
    );
    
    sectionsToAnimate.forEach(section => {
        if (section) {
            sectionObserver.observe(section);
        }
    });
}

// Initialiser les animations au chargement du DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollAnimations);
} else {
    // DOM déjà chargé
    initScrollAnimations();
}
