/**
 * Testimonials carousel functionality
 * Handles testimonial slider, navigation and auto-play
 */

class TestimonialsCarousel {
    constructor() {
        this.carousel = document.getElementById('testimonials-carousel');
        this.slides = document.querySelectorAll('.testimonial-slide');
        this.dots = document.querySelectorAll('.dot');
        this.prevBtn = document.getElementById('prev-btn');
        this.nextBtn = document.getElementById('next-btn');
        this.currentSlide = 0;
        this.autoPlayInterval = null;
        this.autoPlayDelay = 5000; // 5 seconds
        
        this.init();
    }

    init() {
        if (!this.carousel || this.slides.length === 0) return;

        this.setupEventListeners();
        this.startAutoPlay();
        this.addTouchSupport();
        
        console.log('🎠 Testimonials carousel initialized');
    }

    setupEventListeners() {
        // Navigation buttons
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => this.prevSlide());
        }
        
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => this.nextSlide());
        }

        // Dots navigation
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => this.goToSlide(index));
        });

        // Pause auto-play on hover
        this.carousel.addEventListener('mouseenter', () => this.pauseAutoPlay());
        this.carousel.addEventListener('mouseleave', () => this.startAutoPlay());

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.prevSlide();
            if (e.key === 'ArrowRight') this.nextSlide();
        });
    }

    goToSlide(index) {
        // Remove active class from current slide and dot
        this.slides[this.currentSlide].classList.remove('active');
        this.dots[this.currentSlide].classList.remove('active');

        // Update current slide
        this.currentSlide = index;

        // Add active class to new slide and dot
        this.slides[this.currentSlide].classList.add('active');
        this.dots[this.currentSlide].classList.add('active');

        // Update carousel transform
        this.updateCarouselPosition();
    }

    nextSlide() {
        const nextIndex = (this.currentSlide + 1) % this.slides.length;
        this.goToSlide(nextIndex);
    }

    prevSlide() {
        const prevIndex = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
        this.goToSlide(prevIndex);
    }

    updateCarouselPosition() {
        // Add slide transition animation
        this.slides.forEach((slide, index) => {
            slide.style.transform = `translateX(${(index - this.currentSlide) * 100}%)`;
            slide.style.opacity = index === this.currentSlide ? '1' : '0';
        });
    }

    startAutoPlay() {
        this.pauseAutoPlay(); // Clear any existing interval
        this.autoPlayInterval = setInterval(() => {
            this.nextSlide();
        }, this.autoPlayDelay);
    }

    pauseAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        }
    }

    addTouchSupport() {
        let startX = 0;
        let endX = 0;
        let isDragging = false;

        this.carousel.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
            this.pauseAutoPlay();
        });

        this.carousel.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            endX = e.touches[0].clientX;
        });

        this.carousel.addEventListener('touchend', () => {
            if (!isDragging) return;
            isDragging = false;

            const deltaX = startX - endX;
            const threshold = 50; // Minimum swipe distance

            if (Math.abs(deltaX) > threshold) {
                if (deltaX > 0) {
                    this.nextSlide();
                } else {
                    this.prevSlide();
                }
            }

            this.startAutoPlay();
        });
    }

    // Public methods for external control
    pause() {
        this.pauseAutoPlay();
    }

    resume() {
        this.startAutoPlay();
    }

    destroy() {
        this.pauseAutoPlay();
        // Remove event listeners if needed
    }
}

// Testimonials animation effects
class TestimonialsAnimations {
    constructor() {
        this.init();
    }

    init() {
        this.setupScrollAnimations();
        this.setupHoverEffects();
    }

    setupScrollAnimations() {
        const testimonialItems = document.querySelectorAll('.testimonial-item');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('visible');
                    }, index * 100);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        testimonialItems.forEach(item => observer.observe(item));
    }

    setupHoverEffects() {
        const testimonialCards = document.querySelectorAll('.testimonial-mini');
        
        testimonialCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px) scale(1.02)';
                card.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
                card.style.boxShadow = '';
            });
        });
    }
}

// Add testimonials-specific styles
const testimonialsStyles = `
    <style>
        /* Testimonials Stats */
        .testimonials-stats {
            background: var(--background-light);
            padding: var(--space-2xl) 0;
        }

        /* Featured Testimonials */
        .testimonials-carousel {
            position: relative;
            overflow: hidden;
            border-radius: var(--radius-xl);
            margin-bottom: var(--space-xl);
        }

        .testimonial-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.5s ease;
        }

        .testimonial-slide.active {
            position: relative;
            opacity: 1;
            transform: translateX(0);
        }

        .testimonial-card {
            padding: var(--space-2xl);
            text-align: center;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .testimonial-content {
            max-width: 800px;
        }

        .quote-icon {
            color: var(--primary-color);
            margin-bottom: var(--space-lg);
            opacity: 0.3;
        }

        .testimonial-card blockquote {
            font-size: var(--font-size-xl);
            font-style: italic;
            line-height: 1.6;
            color: var(--text-primary);
            margin-bottom: var(--space-xl);
            position: relative;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-lg);
        }

        .author-avatar {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .author-info h4 {
            margin: 0 0 var(--space-xs);
            color: var(--text-primary);
            font-size: var(--font-size-lg);
        }

        .author-info p {
            margin: 0 0 var(--space-sm);
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .rating {
            display: flex;
            gap: 2px;
        }

        .star {
            color: #fbbf24;
            font-size: var(--font-size-lg);
        }

        /* Carousel Controls */
        .carousel-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-lg);
            margin-top: var(--space-xl);
        }

        .carousel-btn {
            width: 50px;
            height: 50px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition-base);
            backdrop-filter: blur(var(--blur));
        }

        .carousel-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .carousel-dots {
            display: flex;
            gap: var(--space-sm);
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--border-color);
            border: none;
            cursor: pointer;
            transition: all var(--transition-base);
        }

        .dot.active {
            background: var(--primary-color);
            transform: scale(1.2);
        }

        /* All Testimonials Grid */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: var(--space-xl);
        }

        .testimonial-item {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .testimonial-item.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .testimonial-mini {
            padding: var(--space-xl);
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all var(--transition-base);
        }

        .testimonial-header {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            margin-bottom: var(--space-md);
        }

        .client-avatar {
            width: 60px;
            height: 60px;
            background: var(--gradient-secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .client-info h4 {
            margin: 0 0 var(--space-xs);
            color: var(--text-primary);
            font-size: var(--font-size-lg);
        }

        .client-info p {
            margin: 0 0 var(--space-xs);
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .testimonial-mini p {
            flex: 1;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: var(--space-md);
        }

        .testimonial-category {
            font-size: var(--font-size-sm);
            color: var(--primary-color);
            font-weight: var(--font-weight-semibold);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: var(--space-xs) var(--space-sm);
            background: rgba(var(--primary-color), 0.1);
            border-radius: var(--radius-full);
            align-self: flex-start;
        }

        /* Trust Indicators */
        .trust-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-xl);
        }

        .trust-item {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .trust-item.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .trust-card {
            padding: var(--space-xl);
            text-align: center;
            height: 100%;
            transition: all var(--transition-base);
        }

        .trust-card:hover {
            transform: translateY(-5px);
        }

        .trust-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin: 0 auto var(--space-md);
        }

        .trust-card h3 {
            margin-bottom: var(--space-sm);
            color: var(--text-primary);
        }

        .trust-card p {
            color: var(--text-secondary);
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            .trust-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .testimonial-card {
                padding: var(--space-lg);
                min-height: 300px;
            }

            .testimonial-card blockquote {
                font-size: var(--font-size-lg);
            }

            .testimonial-author {
                flex-direction: column;
                text-align: center;
            }

            .author-avatar {
                width: 60px;
                height: 60px;
            }
        }

        @media (max-width: 480px) {
            .trust-grid {
                grid-template-columns: 1fr;
            }

            .carousel-controls {
                gap: var(--space-md);
            }

            .carousel-btn {
                width: 40px;
                height: 40px;
            }
        }
    </style>
`;

// Initialize testimonials functionality
document.addEventListener('DOMContentLoaded', () => {
    // Add styles to head
    document.head.insertAdjacentHTML('beforeend', testimonialsStyles);
    
    // Initialize carousel and animations
    window.testimonialsCarousel = new TestimonialsCarousel();
    window.testimonialsAnimations = new TestimonialsAnimations();
    
    console.log('💬 Testimonials system ready');
});

// Export for external use
window.TestimonialsCarousel = TestimonialsCarousel;
window.TestimonialsAnimations = TestimonialsAnimations;