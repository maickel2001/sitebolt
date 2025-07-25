/**
 * Main JavaScript file for MonSite.pro
 * Handles core functionality, navigation, theme switching, and UI interactions
 */

// DOM Elements
const loader = document.getElementById('loader');
const progressBar = document.getElementById('progress-bar');
const header = document.getElementById('header');
const navToggle = document.getElementById('nav-toggle');
const navMenu = document.getElementById('nav-menu');
const navClose = document.getElementById('nav-close');
const themeToggle = document.getElementById('theme-toggle');
const backToTop = document.getElementById('back-to-top');

// Theme Management
class ThemeManager {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'light';
        this.init();
    }

    init() {
        this.setTheme(this.theme);
        if (themeToggle) {
            themeToggle.addEventListener('click', () => this.toggleTheme());
        }
    }

    setTheme(theme) {
        this.theme = theme;
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
    }

    toggleTheme() {
        const newTheme = this.theme === 'light' ? 'dark' : 'light';
        this.setTheme(newTheme);
    }
}

// Navigation Management
class NavigationManager {
    constructor() {
        this.isMenuOpen = false;
        this.init();
    }

    init() {
        // Mobile menu toggle
        if (navToggle) {
            navToggle.addEventListener('click', () => this.toggleMenu());
        }

        if (navClose) {
            navClose.addEventListener('click', () => this.closeMenu());
        }

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (this.isMenuOpen && !navMenu.contains(e.target) && !navToggle.contains(e.target)) {
                this.closeMenu();
            }
        });

        // Close menu when clicking on nav links
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => this.closeMenu());
        });

        // Sticky header
        this.handleStickyHeader();
    }

    toggleMenu() {
        this.isMenuOpen = !this.isMenuOpen;
        navMenu.classList.toggle('active', this.isMenuOpen);
        navToggle.classList.toggle('active', this.isMenuOpen);
        document.body.style.overflow = this.isMenuOpen ? 'hidden' : '';
    }

    closeMenu() {
        this.isMenuOpen = false;
        navMenu.classList.remove('active');
        navToggle.classList.remove('active');
        document.body.style.overflow = '';
    }

    handleStickyHeader() {
        let lastScrollY = window.scrollY;

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;

            if (header) {
                if (currentScrollY > 100) {
                    header.style.background = 'rgba(255, 255, 255, 0.95)';
                    if (document.documentElement.getAttribute('data-theme') === 'dark') {
                        header.style.background = 'rgba(17, 24, 39, 0.95)';
                    }
                } else {
                    header.style.background = 'rgba(255, 255, 255, 0.95)';
                    if (document.documentElement.getAttribute('data-theme') === 'dark') {
                        header.style.background = 'rgba(17, 24, 39, 0.95)';
                    }
                }

                // Hide/show header on scroll
                if (currentScrollY > lastScrollY && currentScrollY > 200) {
                    header.style.transform = 'translateY(-100%)';
                } else {
                    header.style.transform = 'translateY(0)';
                }
            }

            lastScrollY = currentScrollY;
        });
    }
}

// Progress Bar Manager
class ProgressBarManager {
    constructor() {
        this.init();
    }

    init() {
        if (progressBar) {
            window.addEventListener('scroll', () => this.updateProgress());
        }
    }

    updateProgress() {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const progress = (scrollTop / docHeight) * 100;
        progressBar.style.width = `${Math.min(progress, 100)}%`;
    }
}

// Statistics Counter
class StatisticsCounter {
    constructor() {
        this.counters = document.querySelectorAll('.stat-number');
        this.init();
    }

    init() {
        if (this.counters.length > 0) {
            this.observeCounters();
        }
    }

    observeCounters() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        this.counters.forEach(counter => observer.observe(counter));
    }

    animateCounter(element) {
        const target = parseInt(element.getAttribute('data-target')) || 0;
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current);
        }, 16);
    }
}

// Skills Progress Animation
class SkillsAnimator {
    constructor() {
        this.skillBars = document.querySelectorAll('.skill-progress');
        this.init();
    }

    init() {
        if (this.skillBars.length > 0) {
            this.observeSkills();
        }
    }

    observeSkills() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateSkillBar(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        this.skillBars.forEach(bar => observer.observe(bar));
    }

    animateSkillBar(element) {
        const width = element.getAttribute('data-width');
        setTimeout(() => {
            element.style.width = `${width}%`;
        }, 300);
    }
}

// FAQ Manager
class FAQManager {
    constructor() {
        this.faqItems = document.querySelectorAll('.faq-item');
        this.init();
    }

    init() {
        this.faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            if (question) {
                question.addEventListener('click', () => this.toggleFAQ(item));
            }
        });
    }

    toggleFAQ(item) {
        const isActive = item.classList.contains('active');
        
        // Close all FAQ items
        this.faqItems.forEach(faq => {
            faq.classList.remove('active');
            const answer = faq.querySelector('.faq-answer');
            if (answer) {
                answer.style.maxHeight = '0';
            }
        });

        // Open clicked item if it wasn't active
        if (!isActive) {
            item.classList.add('active');
            const answer = item.querySelector('.faq-answer');
            if (answer) {
                answer.style.maxHeight = `${answer.scrollHeight}px`;
            }
        }
    }
}

// Back to Top Button
class BackToTopManager {
    constructor() {
        this.init();
    }

    init() {
        if (backToTop) {
            // Show/hide button based on scroll position
            window.addEventListener('scroll', () => {
                if (window.scrollY > 500) {
                    backToTop.classList.add('visible');
                } else {
                    backToTop.classList.remove('visible');
                }
            });

            // Smooth scroll to top
            backToTop.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    }
}

// Form Validation
class FormValidator {
    constructor() {
        this.forms = document.querySelectorAll('form');
        this.init();
    }

    init() {
        this.forms.forEach(form => {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
            
            // Real-time validation
            const inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => this.validateField(input));
                input.addEventListener('input', () => this.clearErrors(input));
            });
        });
    }

    handleSubmit(e) {
        e.preventDefault();
        const form = e.target;
        
        if (this.validateForm(form)) {
            this.submitForm(form);
        }
    }

    validateForm(form) {
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        let isValid = true;
        let message = '';

        // Required field validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            message = 'Ce champ est requis.';
        }

        // Email validation
        else if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                message = 'Veuillez entrer une adresse email valide.';
            }
        }

        // Phone validation
        else if (type === 'tel' && value) {
            const phoneRegex = /^[+]?[\d\s\-\(\)]{10,}$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                message = 'Veuillez entrer un numéro de téléphone valide.';
            }
        }

        // Name validation
        else if (field.name === 'name' && value) {
            if (value.length < 2) {
                isValid = false;
                message = 'Le nom doit contenir au moins 2 caractères.';
            }
        }

        // Message validation
        else if (field.name === 'message' && value) {
            if (value.length < 10) {
                isValid = false;
                message = 'Le message doit contenir au moins 10 caractères.';
            }
        }

        this.showFieldError(field, isValid ? '' : message);
        return isValid;
    }

    showFieldError(field, message) {
        const existingError = field.parentNode.querySelector('.field-error');
        
        if (existingError) {
            existingError.remove();
        }

        if (message) {
            field.classList.add('error');
            const errorElement = document.createElement('span');
            errorElement.className = 'field-error';
            errorElement.textContent = message;
            errorElement.style.cssText = `
                color: #ef4444;
                font-size: 0.875rem;
                margin-top: 0.25rem;
                display: block;
            `;
            field.parentNode.appendChild(errorElement);
        } else {
            field.classList.remove('error');
        }
    }

    clearErrors(field) {
        field.classList.remove('error');
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    }

    async submitForm(form) {
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        
        // Show loading state
        submitButton.textContent = 'Envoi en cours...';
        submitButton.disabled = true;

        try {
            // Simulate form submission (replace with actual endpoint)
            await new Promise(resolve => setTimeout(resolve, 2000));
            
            // Show success message
            this.showFormMessage(form, 'Votre message a été envoyé avec succès !', 'success');
            form.reset();
            
        } catch (error) {
            // Show error message
            this.showFormMessage(form, 'Une erreur est survenue. Veuillez réessayer.', 'error');
        } finally {
            // Reset button
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    }

    showFormMessage(form, message, type) {
        const existingMessage = form.querySelector('.form-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        const messageElement = document.createElement('div');
        messageElement.className = `form-message ${type}`;
        messageElement.textContent = message;
        messageElement.style.cssText = `
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
            font-weight: 500;
            ${type === 'success' 
                ? 'background-color: #10b981; color: white;' 
                : 'background-color: #ef4444; color: white;'
            }
        `;

        form.appendChild(messageElement);

        // Remove message after 5 seconds
        setTimeout(() => {
            if (messageElement.parentNode) {
                messageElement.remove();
            }
        }, 5000);
    }
}

// Loader Manager
class LoaderManager {
    constructor() {
        this.init();
    }

    init() {
        // Hide loader when page is fully loaded
        window.addEventListener('load', () => {
            setTimeout(() => {
                if (loader) {
                    loader.classList.add('hidden');
                }
            }, 500);
        });

        // Hide loader after maximum time (fallback)
        setTimeout(() => {
            if (loader) {
                loader.classList.add('hidden');
            }
        }, 3000);
    }
}

// Utility Functions
const utils = {
    // Debounce function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Throttle function
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    // Check if element is in viewport
    isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    },

    // Format number with animation
    formatNumber(num) {
        return new Intl.NumberFormat('fr-FR').format(num);
    }
};

// Initialize all components when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize core components
    new LoaderManager();
    new ThemeManager();
    new NavigationManager();
    new ProgressBarManager();
    new BackToTopManager();
    new StatisticsCounter();
    new SkillsAnimator();
    new FAQManager();
    new FormValidator();

    // Add smooth scrolling to all anchor links
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

    console.log('🚀 MonSite.pro loaded successfully!');
});

// Handle page visibility changes
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        document.title = '👋 Revenez vite ! - MonSite.pro';
    } else {
        // Reset title based on current page
        const originalTitle = document.querySelector('title').getAttribute('data-default') 
            ? 'MonSite.pro - Expert-Comptable & Développeur Web'
            : document.title.replace('👋 Revenez vite ! - ', '');
        document.title = originalTitle;
    }
});

// Export for use in other scripts
window.MonSiteUtils = utils;