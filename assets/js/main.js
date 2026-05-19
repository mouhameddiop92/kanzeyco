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
window.addEventListener('scroll', function () {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
    } else {
        navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
    }
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
const impactNumberObserver = new IntersectionObserver(function (entries) {
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
    // Sur mobile le threshold doit être plus bas pour éviter que les sections
    // restent invisibles si l'utilisateur scrolle rapidement
    const isMobile = window.innerWidth <= 768;
    const sectionObserverOptions = {
        threshold: isMobile ? 0.02 : 0.15,
        rootMargin: isMobile ? '0px 0px 0px 0px' : '0px 0px -50px 0px'
    };

    const sectionObserver = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
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

            // Filet de sécurité : si la section est déjà visible au chargement
            // (ex: première section de la page), déclencher immédiatement
            const rect = section.getBoundingClientRect();
            if (rect.top < window.innerHeight) {
                section.classList.add('is-visible');
                sectionObserver.unobserve(section);
            }
        }
    });
}

// Initialiser les animations au chargement du DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollAnimations);
} else {
    initScrollAnimations();
}

// ============================================
// Accès secret au dashboard admin - Triple-clic sur le logo
// ============================================
(function () {
    'use strict';

    let clickCount = 0;
    let clickTimer = null;
    const REQUIRED_CLICKS = 3; // Triple-clic requis
    const CLICK_TIMEOUT = 1000; // Temps maximum entre les clics (en ms)

    // Gestion du triple-clic sur le logo pour accéder au dashboard admin
    const logo = document.getElementById('siteLogo') || document.getElementById('logoLink');
    if (logo) {
        logo.addEventListener('click', function (e) {
            clickCount++;

            // Réinitialiser le compteur si trop de temps s'est écoulé
            if (clickTimer) {
                clearTimeout(clickTimer);
            }

            clickTimer = setTimeout(function () {
                clickCount = 0;
            }, CLICK_TIMEOUT);

            // Si le triple-clic est atteint, rediriger vers le dashboard
            if (clickCount >= REQUIRED_CLICKS) {
                e.preventDefault();
                clickCount = 0;
                clearTimeout(clickTimer);

                // Petit feedback visuel
                logo.style.opacity = '0.5';
                setTimeout(function () {
                    logo.style.opacity = '1';
                }, 200);

                // Rediriger vers le dashboard admin
                window.location.href = 'admin/';
            }
        }, false);
    }
})();