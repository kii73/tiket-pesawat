document.addEventListener('DOMContentLoaded', function() {
    initAnimations();
    
    initInteractiveElements();
    
    initFormValidations();
});

function initAnimations() {
    const animatedElements = document.querySelectorAll('[data-animate]');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const animationClass = entry.target.getAttribute('data-animate');
                const delay = entry.target.getAttribute('data-delay') || '';
                
                entry.target.classList.add(animationClass);
                if (delay) {
                    entry.target.classList.add(delay);
                }
                
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
    
    const heroElements = document.querySelectorAll('.hero-section [data-animate]');
    heroElements.forEach(element => {
        const animationClass = element.getAttribute('data-animate');
        const delay = element.getAttribute('data-delay') || '';
        
        element.classList.add(animationClass);
        if (delay) {
            element.classList.add(delay);
        }
    });
}

function initInteractiveElements() {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    }
    
    const promoCards = document.querySelectorAll('.promo-card');
    promoCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.classList.add('is-hovered');
        });
        
        card.addEventListener('mouseleave', () => {
            card.classList.remove('is-hovered');
        });
    });
    
    const resultCards = document.querySelectorAll('.result-card');
    resultCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.classList.add('is-hovered');
        });
        
        card.addEventListener('mouseleave', () => {
            card.classList.remove('is-hovered');
        });
    });
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

function initFormValidations() {
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm-password');
    
    if (passwordField && confirmPasswordField) {
        confirmPasswordField.addEventListener('input', () => {
            if (passwordField.value !== confirmPasswordField.value) {
                confirmPasswordField.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordField.setCustomValidity('');
            }
        });
        
        passwordField.addEventListener('input', () => {
            if (confirmPasswordField.value) {
                if (passwordField.value !== confirmPasswordField.value) {
                    confirmPasswordField.setCustomValidity('Passwords do not match');
                } else {
                    confirmPasswordField.setCustomValidity('');
                }
            }
        });
    }
}

function handleLogOut(url="") {
    document.cookie = "remember_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    window.location.replace(url ? url : "login.php");
}