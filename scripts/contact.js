/**
 * Contact form functionality
 * Handles form validation, submission and user feedback
 */

class ContactForm {
    constructor() {
        this.form = document.getElementById('contact-form');
        this.submitBtn = this.form?.querySelector('button[type="submit"]');
        this.btnText = this.submitBtn?.querySelector('.btn-text');
        this.btnLoading = this.submitBtn?.querySelector('.btn-loading');
        
        this.validationRules = {
            name: {
                required: true,
                minLength: 2,
                pattern: /^[a-zA-ZÀ-ÿ\s'-]+$/,
                message: 'Le nom doit contenir au moins 2 caractères et uniquement des lettres'
            },
            email: {
                required: true,
                pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                message: 'Veuillez entrer une adresse email valide'
            },
            phone: {
                required: false,
                pattern: /^[+]?[\d\s\-\(\)]{10,}$/,
                message: 'Veuillez entrer un numéro de téléphone valide'
            },
            message: {
                required: true,
                minLength: 10,
                message: 'Le message doit contenir au moins 10 caractères'
            },
            service: {
                required: true,
                type: 'checkbox',
                message: 'Veuillez sélectionner au moins un service'
            },
            privacy: {
                required: true,
                type: 'checkbox',
                message: 'Vous devez accepter la politique de confidentialité'
            }
        };

        this.init();
    }

    init() {
        if (!this.form) return;

        this.setupEventListeners();
        this.setupRealTimeValidation();
        this.setupFormEnhancements();
        
        console.log('📧 Contact form initialized');
    }

    setupEventListeners() {
        // Form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Real-time validation on blur
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });

        // Checkbox groups special handling
        const serviceCheckboxes = this.form.querySelectorAll('input[name="service"]');
        serviceCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => this.validateCheckboxGroup('service'));
        });

        // Privacy checkbox
        const privacyCheckbox = this.form.querySelector('input[name="privacy"]');
        if (privacyCheckbox) {
            privacyCheckbox.addEventListener('change', () => this.validateField(privacyCheckbox));
        }
    }

    setupRealTimeValidation() {
        // Add visual feedback for form fields
        const inputs = this.form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            // Add focus effects
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('focused');
                if (input.value) {
                    input.parentElement.classList.add('filled');
                } else {
                    input.parentElement.classList.remove('filled');
                }
            });

            // Initialize filled state
            if (input.value) {
                input.parentElement.classList.add('filled');
            }
        });
    }

    setupFormEnhancements() {
        // Auto-resize textarea
        const textarea = this.form.querySelector('textarea');
        if (textarea) {
            textarea.addEventListener('input', () => {
                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
            });
        }

        // Format phone number
        const phoneInput = this.form.querySelector('input[name="phone"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 0) {
                    value = value.replace(/(\d{2})(?=\d)/g, '$1 ');
                }
                e.target.value = value;
            });
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        // Validate entire form
        if (!this.validateForm()) {
            this.showFormMessage('Veuillez corriger les erreurs dans le formulaire.', 'error');
            return;
        }

        // Show loading state
        this.setLoadingState(true);

        try {
            // Collect form data
            const formData = this.collectFormData();
            
            // Simulate form submission (replace with actual endpoint)
            await this.submitForm(formData);
            
            // Show success message
            this.showFormMessage('Votre message a été envoyé avec succès ! Je vous recontacterai sous 24h.', 'success');
            
            // Reset form
            this.form.reset();
            this.clearAllErrors();
            
            // Track conversion (analytics)
            this.trackFormSubmission(formData);
            
        } catch (error) {
            console.error('Form submission error:', error);
            this.showFormMessage('Une erreur est survenue. Veuillez réessayer ou me contacter directement.', 'error');
        } finally {
            this.setLoadingState(false);
        }
    }

    validateForm() {
        let isValid = true;
        
        // Validate all fields
        Object.keys(this.validationRules).forEach(fieldName => {
            if (!this.validateField(this.getField(fieldName))) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(field) {
        if (!field) return true;

        const fieldName = field.name;
        const rules = this.validationRules[fieldName];
        
        if (!rules) return true;

        let value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Handle checkbox groups
        if (rules.type === 'checkbox') {
            if (fieldName === 'service') {
                return this.validateCheckboxGroup(fieldName);
            } else {
                value = field.checked;
            }
        }

        // Required validation
        if (rules.required) {
            if (rules.type === 'checkbox') {
                if (!value) {
                    isValid = false;
                    errorMessage = rules.message;
                }
            } else if (!value) {
                isValid = false;
                errorMessage = 'Ce champ est requis.';
            }
        }

        // Skip other validations if field is empty and not required
        if (!value && !rules.required) {
            this.clearFieldError(field);
            return true;
        }

        // Pattern validation
        if (isValid && rules.pattern && value && !rules.pattern.test(value)) {
            isValid = false;
            errorMessage = rules.message;
        }

        // Min length validation
        if (isValid && rules.minLength && value.length < rules.minLength) {
            isValid = false;
            errorMessage = rules.message;
        }

        // Show/hide error
        if (isValid) {
            this.clearFieldError(field);
        } else {
            this.showFieldError(field, errorMessage);
        }

        return isValid;
    }

    validateCheckboxGroup(groupName) {
        const checkboxes = this.form.querySelectorAll(`input[name="${groupName}"]`);
        const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
        const isValid = checkedBoxes.length > 0;
        
        const firstCheckbox = checkboxes[0];
        if (isValid) {
            this.clearFieldError(firstCheckbox);
        } else {
            this.showFieldError(firstCheckbox, this.validationRules[groupName].message);
        }

        return isValid;
    }

    getField(fieldName) {
        return this.form.querySelector(`[name="${fieldName}"]`);
    }

    showFieldError(field, message) {
        this.clearFieldError(field);
        
        field.classList.add('error');
        
        const errorElement = document.createElement('span');
        errorElement.className = 'field-error';
        errorElement.textContent = message;
        
        // Insert error message after the field or its container
        const container = field.closest('.form-group') || field.parentElement;
        container.appendChild(errorElement);
    }

    clearFieldError(field) {
        field.classList.remove('error');
        
        const container = field.closest('.form-group') || field.parentElement;
        const existingError = container.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    }

    clearAllErrors() {
        const errorElements = this.form.querySelectorAll('.field-error');
        errorElements.forEach(error => error.remove());
        
        const errorFields = this.form.querySelectorAll('.error');
        errorFields.forEach(field => field.classList.remove('error'));
    }

    collectFormData() {
        const formData = new FormData(this.form);
        const data = {};
        
        // Handle regular fields
        for (let [key, value] of formData.entries()) {
            if (data[key]) {
                // Handle multiple values (checkboxes)
                if (Array.isArray(data[key])) {
                    data[key].push(value);
                } else {
                    data[key] = [data[key], value];
                }
            } else {
                data[key] = value;
            }
        }

        // Handle checkboxes that weren't checked
        const checkboxes = this.form.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            if (!checkbox.checked && checkbox.name !== 'service') {
                data[checkbox.name] = false;
            }
        });

        return data;
    }

    async submitForm(formData) {
        // Simulate API call
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                // Simulate success/failure
                if (Math.random() > 0.1) { // 90% success rate
                    resolve({ success: true, message: 'Form submitted successfully' });
                } else {
                    reject(new Error('Submission failed'));
                }
            }, 2000);
        });
    }

    setLoadingState(isLoading) {
        if (!this.submitBtn) return;

        this.submitBtn.disabled = isLoading;
        
        if (isLoading) {
            this.btnText.style.display = 'none';
            this.btnLoading.style.display = 'flex';
            this.submitBtn.classList.add('loading');
        } else {
            this.btnText.style.display = 'inline';
            this.btnLoading.style.display = 'none';
            this.submitBtn.classList.remove('loading');
        }
    }

    showFormMessage(message, type) {
        // Remove existing message
        const existingMessage = this.form.querySelector('.form-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        // Create new message
        const messageElement = document.createElement('div');
        messageElement.className = `form-message ${type}`;
        messageElement.innerHTML = `
            <div class="message-icon">
                ${type === 'success' 
                    ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/></svg>'
                    : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M15 9l-6 6M9 9l6 6" stroke="currentColor" stroke-width="2"/></svg>'
                }
            </div>
            <span>${message}</span>
        `;

        // Insert message
        this.form.appendChild(messageElement);

        // Auto-remove after 8 seconds
        setTimeout(() => {
            if (messageElement.parentElement) {
                messageElement.remove();
            }
        }, 8000);

        // Scroll to message
        messageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    trackFormSubmission(formData) {
        // Analytics tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', 'form_submit', {
                event_category: 'Contact',
                event_label: 'Contact Form',
                value: 1
            });
        }

        console.log('📊 Form submission tracked:', formData);
    }
}

// Add contact-specific styles
const contactStyles = `
    <style>
        /* Contact Methods */
        .contact-methods {
            padding: var(--space-2xl) 0;
            background: var(--background-light);
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-xl);
        }

        .contact-card {
            padding: var(--space-xl);
            text-align: center;
            transition: all var(--transition-base);
        }

        .contact-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin: 0 auto var(--space-md);
        }

        .contact-card h3 {
            margin-bottom: var(--space-sm);
            color: var(--text-primary);
        }

        .contact-card p {
            color: var(--text-secondary);
            margin-bottom: var(--space-sm);
        }

        .contact-link {
            color: var(--primary-color);
            font-weight: var(--font-weight-semibold);
            text-decoration: none;
            transition: color var(--transition-base);
        }

        .contact-link:hover {
            color: var(--primary-dark);
        }

        /* Contact Form Section */
        .contact-form-section {
            padding: var(--space-2xl) 0;
        }

        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-2xl);
            align-items: start;
        }

        .contact-info {
            padding-right: var(--space-lg);
        }

        .contact-benefits {
            margin: var(--space-xl) 0;
        }

        .benefit-item {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            margin-bottom: var(--space-lg);
        }

        .benefit-icon {
            width: 40px;
            height: 40px;
            background: var(--gradient-secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .benefit-item h4 {
            margin: 0 0 var(--space-xs);
            color: var(--text-primary);
        }

        .benefit-item p {
            margin: 0;
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .contact-cta {
            padding: var(--space-xl);
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            backdrop-filter: blur(var(--blur));
        }

        .contact-cta h3 {
            margin-bottom: var(--space-sm);
            color: var(--text-primary);
        }

        .contact-cta p {
            margin-bottom: var(--space-md);
            color: var(--text-secondary);
        }

        .emergency-contacts {
            display: flex;
            gap: var(--space-sm);
            flex-wrap: wrap;
        }

        /* Contact Form */
        .contact-form-container {
            position: sticky;
            top: calc(var(--header-height) + var(--space-lg));
        }

        .contact-form {
            padding: var(--space-2xl);
        }

        .form-group {
            margin-bottom: var(--space-lg);
            position: relative;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-md);
        }

        .form-group label {
            display: block;
            margin-bottom: var(--space-xs);
            color: var(--text-primary);
            font-weight: var(--font-weight-medium);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: var(--space-md);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-lg);
            background: var(--background);
            color: var(--text-primary);
            font-family: inherit;
            font-size: var(--font-size-base);
            transition: all var(--transition-base);
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group.focused label {
            color: var(--primary-color);
        }

        .form-group.error input,
        .form-group.error textarea,
        .form-group.error select {
            border-color: #ef4444;
        }

        .field-error {
            display: block;
            color: #ef4444;
            font-size: var(--font-size-sm);
            margin-top: var(--space-xs);
        }

        /* Checkbox Group */
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--space-sm);
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            cursor: pointer;
            padding: var(--space-sm);
            border-radius: var(--radius-base);
            transition: background-color var(--transition-base);
        }

        .checkbox-item:hover {
            background: var(--background-light);
        }

        .checkbox-item input[type="checkbox"] {
            display: none;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-sm);
            position: relative;
            transition: all var(--transition-base);
            flex-shrink: 0;
        }

        .checkbox-item input[type="checkbox"]:checked + .checkmark {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .checkbox-item input[type="checkbox"]:checked + .checkmark::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .privacy-checkbox {
            margin-top: var(--space-md);
            padding: var(--space-md);
            background: var(--background-light);
            border-radius: var(--radius-lg);
        }

        /* Submit Button */
        .btn-full {
            width: 100%;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .btn-loading {
            display: none;
            align-items: center;
            gap: var(--space-sm);
        }

        .btn.loading {
            pointer-events: none;
        }

        /* Form Message */
        .form-message {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-md);
            border-radius: var(--radius-lg);
            margin-top: var(--space-lg);
            font-weight: var(--font-weight-medium);
            animation: slideIn 0.3s ease;
        }

        .form-message.success {
            background: #10b981;
            color: white;
        }

        .form-message.error {
            background: #ef4444;
            color: white;
        }

        .message-icon {
            flex-shrink: 0;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Map Section */
        .map-container {
            padding: var(--space-2xl);
            text-align: center;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .map-icon {
            color: var(--primary-color);
            margin-bottom: var(--space-md);
        }

        .map-container h3 {
            margin-bottom: var(--space-sm);
            color: var(--text-primary);
        }

        .map-container p {
            margin-bottom: var(--space-lg);
            color: var(--text-secondary);
        }

        .map-info {
            display: flex;
            gap: var(--space-lg);
            flex-wrap: wrap;
            justify-content: center;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .info-item svg {
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .contact-content {
                grid-template-columns: 1fr;
                gap: var(--space-xl);
            }

            .contact-form-container {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .contact-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .checkbox-group {
                grid-template-columns: 1fr;
            }

            .emergency-contacts {
                flex-direction: column;
            }

            .map-info {
                flex-direction: column;
                gap: var(--space-sm);
            }
        }

        @media (max-width: 480px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .contact-form {
                padding: var(--space-lg);
            }
        }
    </style>
`;

// Initialize contact functionality
document.addEventListener('DOMContentLoaded', () => {
    // Add styles to head
    document.head.insertAdjacentHTML('beforeend', contactStyles);
    
    // Initialize contact form
    window.contactForm = new ContactForm();
    
    console.log('📞 Contact system ready');
});

// Export for external use
window.ContactForm = ContactForm;