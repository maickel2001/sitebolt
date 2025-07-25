/**
 * Animation Controller for MonSite.pro
 * Handles scroll animations, intersection observers, and visual effects
 */

class AnimationController {
    constructor() {
        this.animatedElements = new Set();
        this.observers = new Map();
        this.init();
    }

    init() {
        this.setupIntersectionObservers();
        this.setupScrollAnimations();
        this.setupParallaxEffects();
        this.initializeAnimations();
    }

    setupIntersectionObservers() {
        // Fade up animation observer
        const fadeUpObserver = new IntersectionObserver(
            (entries) => this.handleFadeUpAnimations(entries),
            {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            }
        );

        // Scale in animation observer
        const scaleInObserver = new IntersectionObserver(
            (entries) => this.handleScaleInAnimations(entries),
            {
                threshold: 0.1,
                rootMargin: '0px 0px -30px 0px'
            }
        );

        // Slide animations observer
        const slideObserver = new IntersectionObserver(
            (entries) => this.handleSlideAnimations(entries),
            {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            }
        );

        // Store observers
        this.observers.set('fadeUp', fadeUpObserver);
        this.observers.set('scaleIn', scaleInObserver);
        this.observers.set('slide', slideObserver);

        // Observe elements
        this.observeElements();
    }

    observeElements() {
        // Fade up elements
        const fadeUpElements = document.querySelectorAll('.fade-up');
        fadeUpElements.forEach(el => {
            this.observers.get('fadeUp').observe(el);
        });

        // Scale in elements
        const scaleInElements = document.querySelectorAll('.scale-in');
        scaleInElements.forEach(el => {
            this.observers.get('scaleIn').observe(el);
        });

        // Slide elements
        const slideElements = document.querySelectorAll('.slide-in-left, .slide-in-right');
        slideElements.forEach(el => {
            this.observers.get('slide').observe(el);
        });
    }

    handleFadeUpAnimations(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !this.animatedElements.has(entry.target)) {
                entry.target.classList.add('visible');
                this.animatedElements.add(entry.target);
                
                // Add stagger delay for children if parent has stagger class
                if (entry.target.classList.contains('stagger-children')) {
                    this.staggerChildren(entry.target);
                }
            }
        });
    }

    handleScaleInAnimations(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !this.animatedElements.has(entry.target)) {
                entry.target.classList.add('visible');
                this.animatedElements.add(entry.target);
            }
        });
    }

    handleSlideAnimations(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !this.animatedElements.has(entry.target)) {
                entry.target.classList.add('visible');
                this.animatedElements.add(entry.target);
            }
        });
    }

    staggerChildren(parent) {
        const children = parent.children;
        Array.from(children).forEach((child, index) => {
            child.style.transitionDelay = `${index * 0.1}s`;
            setTimeout(() => {
                child.classList.add('visible');
            }, index * 100);
        });
    }

    setupScrollAnimations() {
        let ticking = false;

        const updateScrollAnimations = () => {
            this.handleParallaxScroll();
            this.updateProgressIndicators();
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateScrollAnimations);
                ticking = true;
            }
        });
    }

    setupParallaxEffects() {
        this.parallaxElements = document.querySelectorAll('.parallax-element');
        this.floatingElements = document.querySelectorAll('.floating-element');
    }

    handleParallaxScroll() {
        const scrollY = window.pageYOffset;

        // Parallax elements
        this.parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            const yPos = -(scrollY * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });

        // Floating elements
        this.floatingElements.forEach((element, index) => {
            const speed = 0.5 + (index * 0.1);
            const yPos = Math.sin(scrollY * 0.01 + index) * 20;
            element.style.transform = `translateY(${yPos}px)`;
        });
    }

    updateProgressIndicators() {
        // Update any progress bars or indicators based on scroll position
        const progressElements = document.querySelectorAll('[data-progress]');
        
        progressElements.forEach(element => {
            const rect = element.getBoundingClientRect();
            const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
            
            if (isVisible) {
                const progress = Math.max(0, Math.min(1, 
                    (window.innerHeight - rect.top) / window.innerHeight
                ));
                
                element.style.setProperty('--progress', progress);
            }
        });
    }

    initializeAnimations() {
        // Initialize any special animations that need setup
        this.initializeTypingAnimations();
        this.initializeCounterAnimations();
        this.initializeHoverEffects();
    }

    initializeTypingAnimations() {
        const typingElements = document.querySelectorAll('.typing-animation');
        
        typingElements.forEach(element => {
            const text = element.textContent;
            element.textContent = '';
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.typeText(element, text);
                        observer.unobserve(element);
                    }
                });
            });
            
            observer.observe(element);
        });
    }

    typeText(element, text) {
        let index = 0;
        
        const typeChar = () => {
            if (index < text.length) {
                element.textContent += text.charAt(index);
                index++;
                setTimeout(typeChar, 100);
            }
        };
        
        typeChar();
    }

    initializeCounterAnimations() {
        const counterElements = document.querySelectorAll('.counter');
        
        counterElements.forEach(element => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.animateCounter(element);
                        observer.unobserve(element);
                    }
                });
            });
            
            observer.observe(element);
        });
    }

    animateCounter(element) {
        const target = parseInt(element.getAttribute('data-target')) || 0;
        const duration = parseInt(element.getAttribute('data-duration')) || 2000;
        const start = performance.now();
        
        const updateCounter = (currentTime) => {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(target * easeOut);
            
            element.textContent = this.formatNumber(current);
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = this.formatNumber(target);
            }
        };
        
        requestAnimationFrame(updateCounter);
    }

    formatNumber(num) {
        return new Intl.NumberFormat('fr-FR').format(num);
    }

    initializeHoverEffects() {
        // Add magnetic effect to buttons
        const magneticElements = document.querySelectorAll('.btn, .service-card, .value-card');
        
        magneticElements.forEach(element => {
            element.addEventListener('mousemove', (e) => {
                const rect = element.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                const moveX = x * 0.1;
                const moveY = y * 0.1;
                
                element.style.transform = `translate(${moveX}px, ${moveY}px)`;
            });
            
            element.addEventListener('mouseleave', () => {
                element.style.transform = 'translate(0, 0)';
            });
        });

        // Add ripple effect to buttons
        const rippleButtons = document.querySelectorAll('.btn');
        
        rippleButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                this.createRippleEffect(button, e);
            });
        });
    }

    createRippleEffect(element, event) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple 0.6s linear;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            pointer-events: none;
        `;
        
        // Add ripple animation if not exists
        if (!document.querySelector('#ripple-styles')) {
            const style = document.createElement('style');
            style.id = 'ripple-styles';
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    // Public methods for manual animation control
    animateElement(element, animationType) {
        switch (animationType) {
            case 'fadeUp':
                element.classList.add('fade-up', 'visible');
                break;
            case 'scaleIn':
                element.classList.add('scale-in', 'visible');
                break;
            case 'slideInLeft':
                element.classList.add('slide-in-left', 'visible');
                break;
            case 'slideInRight':
                element.classList.add('slide-in-right', 'visible');
                break;
            default:
                console.warn(`Unknown animation type: ${animationType}`);
        }
    }

    resetAnimation(element) {
        const animationClasses = ['visible', 'fade-up', 'scale-in', 'slide-in-left', 'slide-in-right'];
        element.classList.remove(...animationClasses);
        this.animatedElements.delete(element);
    }

    // Cleanup method
    destroy() {
        this.observers.forEach(observer => observer.disconnect());
        this.observers.clear();
        this.animatedElements.clear();
    }
}

// Page transition effects
class PageTransitionManager {
    constructor() {
        this.init();
    }

    init() {
        // Add page transition styles if not present
        if (!document.querySelector('#page-transition-styles')) {
            this.addTransitionStyles();
        }

        // Handle page transitions
        this.setupPageTransitions();
    }

    addTransitionStyles() {
        const style = document.createElement('style');
        style.id = 'page-transition-styles';
        style.textContent = `
            .page-transition-enter {
                opacity: 0;
                transform: translateY(20px);
            }
            
            .page-transition-enter-active {
                animation: pageEnter 0.5s ease forwards;
            }
            
            @keyframes pageEnter {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    }

    setupPageTransitions() {
        // Add entrance animation to main content
        const main = document.querySelector('main');
        if (main) {
            main.classList.add('page-transition-enter');
            
            // Trigger animation after a short delay
            setTimeout(() => {
                main.classList.add('page-transition-enter-active');
            }, 100);
        }
    }
}

// Scroll-triggered animations for specific elements
class ScrollTriggerAnimations {
    constructor() {
        this.triggers = [];
        this.init();
    }

    init() {
        this.setupScrollTriggers();
        this.bindScrollListener();
    }

    setupScrollTriggers() {
        // Define scroll-triggered animations
        this.addTrigger({
            element: '.hero-title',
            animation: 'slideUp',
            offset: 0.8
        });

        this.addTrigger({
            element: '.stats-section .stat-card',
            animation: 'staggerUp',
            offset: 0.7,
            stagger: 0.1
        });

        this.addTrigger({
            element: '.timeline-item',
            animation: 'slideInLeft',
            offset: 0.6,
            stagger: 0.2
        });
    }

    addTrigger(config) {
        const elements = document.querySelectorAll(config.element);
        
        elements.forEach((element, index) => {
            this.triggers.push({
                element,
                animation: config.animation,
                offset: config.offset || 0.8,
                delay: config.stagger ? index * config.stagger : 0,
                triggered: false
            });
        });
    }

    bindScrollListener() {
        let ticking = false;

        const handleScroll = () => {
            this.checkTriggers();
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(handleScroll);
                ticking = true;
            }
        });
    }

    checkTriggers() {
        const windowHeight = window.innerHeight;
        const scrollTop = window.pageYOffset;

        this.triggers.forEach(trigger => {
            if (trigger.triggered) return;

            const elementTop = trigger.element.getBoundingClientRect().top + scrollTop;
            const triggerPoint = windowHeight * trigger.offset;

            if (scrollTop + triggerPoint > elementTop) {
                setTimeout(() => {
                    this.executeAnimation(trigger);
                }, trigger.delay * 1000);
                
                trigger.triggered = true;
            }
        });
    }

    executeAnimation(trigger) {
        const { element, animation } = trigger;

        switch (animation) {
            case 'slideUp':
                element.style.animation = 'slideUp 0.8s ease forwards';
                break;
            case 'staggerUp':
                element.style.animation = 'fadeUp 0.8s ease forwards';
                break;
            case 'slideInLeft':
                element.style.animation = 'slideInLeft 0.8s ease forwards';
                break;
            default:
                element.classList.add('animate-' + animation);
        }
    }
}

// Initialize animation system
document.addEventListener('DOMContentLoaded', () => {
    // Check for reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    if (!prefersReducedMotion) {
        // Initialize animation controllers
        window.animationController = new AnimationController();
        window.pageTransitionManager = new PageTransitionManager();
        window.scrollTriggerAnimations = new ScrollTriggerAnimations();
        
        console.log('🎨 Animation system initialized');
    } else {
        console.log('🎨 Animations disabled due to user preference');
        
        // Add fallback styles for reduced motion
        const style = document.createElement('style');
        style.textContent = `
            .fade-up, .scale-in, .slide-in-left, .slide-in-right {
                opacity: 1 !important;
                transform: none !important;
            }
        `;
        document.head.appendChild(style);
    }
});

// Export for external use
window.AnimationController = AnimationController;
window.PageTransitionManager = PageTransitionManager;
window.ScrollTriggerAnimations = ScrollTriggerAnimations;