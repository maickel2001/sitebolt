/**
 * Chatbot demo functionality
 * Handles the demonstration chatbot on the chatbot page
 */

class ChatbotDemo {
    constructor() {
        this.demoMessages = document.getElementById('demo-messages');
        this.demoInput = document.getElementById('demo-input');
        this.demoSend = document.getElementById('demo-send');
        this.isMinimized = false;
        
        this.responses = {
            'quels sont vos services': `🔧 **Mes Services :**

💼 **Comptabilité & Fiscalité**
• Tenue de comptabilité complète
• Déclarations fiscales (TVA, IS, CFE)
• Optimisation fiscale et conseil

💻 **Développement Web**
• Sites web sur mesure
• Applications web complexes
• Maintenance et support technique

🎯 **Conseil Digital**
• Audit et stratégie digitale
• Formation aux outils numériques
• Accompagnement transformation digitale

Quel service vous intéresse le plus ?`,

            'quels sont vos tarifs': `💰 **Mes Tarifs :**

**Comptabilité :**
• Tenue comptable : à partir de 150€/mois
• Déclarations TVA : 80€/déclaration
• Bilan annuel : 500€

**Développement Web :**
• Site vitrine : à partir de 1500€
• Application web : à partir de 3000€
• Maintenance : 100€/mois

**Conseil Digital :**
• Audit : 500€
• Formation : 200€/jour
• Stratégie : sur devis

💡 Tous mes devis sont gratuits et personnalisés !`,

            'comment vous contacter': `📞 **Me Contacter :**

📧 **Email :** contact@monsite.pro
📱 **Téléphone :** +33 1 23 45 67 89
📍 **Adresse :** Paris, France

🕒 **Disponibilités :**
• Lundi - Vendredi : 9h - 18h
• Samedi : 9h - 12h (sur RDV)

⚡ **Urgences :** Disponible 7j/7 pour les déclarations urgentes

Préférez-vous un appel ou un email ?`,

            'quels sont vos horaires': `🕒 **Mes Horaires :**

**Lundi - Vendredi :**
9h00 - 12h30 / 14h00 - 18h00

**Samedi :**
9h00 - 12h00 (sur rendez-vous uniquement)

**Dimanche :** Fermé

⚡ **Services d'urgence :**
• Déclarations fiscales urgentes : 7j/7
• Support technique sites web : 24h/7j

📅 Souhaitez-vous prendre rendez-vous ?`
        };
        
        this.init();
    }

    init() {
        if (!this.demoMessages || !this.demoInput) return;

        this.setupEventListeners();
        console.log('🤖 Chatbot demo initialized');
    }

    setupEventListeners() {
        // Send button click
        if (this.demoSend) {
            this.demoSend.addEventListener('click', () => this.sendMessage());
        }

        // Enter key press
        if (this.demoInput) {
            this.demoInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.sendMessage();
                }
            });
        }
    }

    sendMessage() {
        const message = this.demoInput.value.trim();
        if (!message) return;

        // Add user message
        this.addMessage(message, 'user');
        
        // Clear input
        this.demoInput.value = '';
        
        // Show typing indicator
        this.showTypingIndicator();
        
        // Process and respond
        setTimeout(() => {
            this.hideTypingIndicator();
            const response = this.processMessage(message);
            this.addMessage(response, 'bot');
        }, 1000 + Math.random() * 1500);
    }

    addMessage(content, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const messageAvatar = document.createElement('div');
        messageAvatar.className = 'message-avatar';
        
        if (sender === 'bot') {
            messageAvatar.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2"/>
                </svg>
            `;
        } else {
            messageAvatar.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                </svg>
            `;
        }
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        messageContent.innerHTML = this.formatMessage(content);
        
        const messageTime = document.createElement('div');
        messageTime.className = 'message-time';
        messageTime.textContent = this.getCurrentTime();
        
        messageDiv.appendChild(messageAvatar);
        messageDiv.appendChild(messageContent);
        messageDiv.appendChild(messageTime);
        
        this.demoMessages.appendChild(messageDiv);
        this.scrollToBottom();
        
        // Add animation
        setTimeout(() => {
            messageDiv.classList.add('visible');
        }, 50);
    }

    processMessage(message) {
        const normalizedMessage = message.toLowerCase().trim();
        
        // Check for exact matches first
        for (const [key, response] of Object.entries(this.responses)) {
            if (normalizedMessage.includes(key)) {
                return response;
            }
        }
        
        // Check for keywords
        if (normalizedMessage.includes('service') || normalizedMessage.includes('prestation')) {
            return this.responses['quels sont vos services'];
        }
        
        if (normalizedMessage.includes('prix') || normalizedMessage.includes('tarif') || normalizedMessage.includes('coût')) {
            return this.responses['quels sont vos tarifs'];
        }
        
        if (normalizedMessage.includes('contact') || normalizedMessage.includes('joindre') || normalizedMessage.includes('téléphone')) {
            return this.responses['comment vous contacter'];
        }
        
        if (normalizedMessage.includes('horaire') || normalizedMessage.includes('ouvert') || normalizedMessage.includes('disponible')) {
            return this.responses['quels sont vos horaires'];
        }
        
        // Greetings
        if (normalizedMessage.includes('bonjour') || normalizedMessage.includes('salut') || normalizedMessage.includes('hello')) {
            return `Bonjour ! 👋 Je suis ravi de vous accueillir sur le site de MonSite.pro.

Je peux vous renseigner sur :
• Les services proposés
• Les tarifs et devis
• Les modalités de contact
• Les horaires de disponibilité

Que souhaitez-vous savoir ?`;
        }
        
        // Thanks
        if (normalizedMessage.includes('merci') || normalizedMessage.includes('parfait')) {
            return `Je vous en prie ! 😊 

N'hésitez pas si vous avez d'autres questions. Je suis là pour vous aider à trouver la solution qui correspond à vos besoins.

Souhaitez-vous que je vous mette en relation avec mon créateur pour un devis personnalisé ?`;
        }
        
        // Default responses with suggestions
        const suggestions = [
            "Voici quelques questions que vous pouvez me poser :",
            "• Quels sont vos services ?",
            "• Quels sont vos tarifs ?",
            "• Comment vous contacter ?",
            "• Quels sont vos horaires ?",
            "",
            "Ou cliquez sur les boutons de suggestion ci-dessous ! 👇"
        ];
        
        return `Je ne suis pas sûr de comprendre votre question. 🤔

${suggestions.join('\n')}`;
    }

    formatMessage(message) {
        return message
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }

    showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot-message typing-indicator';
        typingDiv.innerHTML = `
            <div class="message-avatar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <div class="message-content">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="message-time">En train d'écrire...</div>
        `;
        
        this.demoMessages.appendChild(typingDiv);
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        const typingIndicator = this.demoMessages.querySelector('.typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    scrollToBottom() {
        this.demoMessages.scrollTop = this.demoMessages.scrollHeight;
    }

    getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString('fr-FR', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    // Public methods for external control
    sendSuggestion(message) {
        this.demoInput.value = message;
        this.sendMessage();
    }

    clearChat() {
        this.demoMessages.innerHTML = `
            <div class="message bot-message visible">
                <div class="message-avatar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <div class="message-content">
                    Bonjour ! Je suis l'assistant virtuel de MonSite.pro. Comment puis-je vous aider aujourd'hui ? 😊
                </div>
                <div class="message-time">${this.getCurrentTime()}</div>
            </div>
        `;
    }

    toggleMinimize() {
        const container = document.querySelector('.chatbot-container');
        const messages = document.querySelector('.chatbot-messages-demo');
        const input = document.querySelector('.chatbot-input-demo');
        
        this.isMinimized = !this.isMinimized;
        
        if (this.isMinimized) {
            messages.style.display = 'none';
            input.style.display = 'none';
            container.style.height = '60px';
        } else {
            messages.style.display = 'block';
            input.style.display = 'flex';
            container.style.height = 'auto';
        }
    }
}

// Global functions for demo interaction
function sendSuggestion(message) {
    if (window.chatbotDemo) {
        window.chatbotDemo.sendSuggestion(message);
    }
}

function sendDemoMessage() {
    if (window.chatbotDemo) {
        window.chatbotDemo.sendMessage();
    }
}

function handleDemoKeyPress(event) {
    if (event.key === 'Enter') {
        sendDemoMessage();
    }
}

function toggleChatbot() {
    if (window.chatbotDemo) {
        window.chatbotDemo.toggleMinimize();
    }
}

// Add demo-specific styles
const demoStyles = `
    <style>
        /* Chatbot Demo */
        .chatbot-demo {
            padding: var(--space-2xl) 0;
        }

        .demo-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-2xl);
            align-items: start;
        }

        .demo-features {
            margin: var(--space-xl) 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            margin-bottom: var(--space-lg);
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .feature-item h4 {
            margin: 0 0 var(--space-xs);
            color: var(--text-primary);
        }

        .feature-item p {
            margin: 0;
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .demo-suggestions {
            margin-top: var(--space-xl);
            padding: var(--space-lg);
            background: var(--background-light);
            border-radius: var(--radius-lg);
        }

        .demo-suggestions h3 {
            margin-bottom: var(--space-md);
            color: var(--text-primary);
            font-size: var(--font-size-lg);
        }

        .suggestion-buttons {
            display: flex;
            flex-direction: column;
            gap: var(--space-sm);
        }

        .suggestion-btn {
            padding: var(--space-sm) var(--space-md);
            background: var(--background);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all var(--transition-base);
            text-align: left;
            font-size: var(--font-size-sm);
        }

        .suggestion-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            transform: translateX(5px);
        }

        /* Demo Chatbot Container */
        .demo-chatbot {
            position: sticky;
            top: calc(var(--header-height) + var(--space-lg));
        }

        .chatbot-container {
            width: 100%;
            max-width: 400px;
            height: 600px;
            border-radius: var(--radius-xl);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: height var(--transition-base);
        }

        .chatbot-header-demo {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            padding: var(--space-lg);
            background: var(--gradient-primary);
            color: white;
        }

        .chatbot-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .chatbot-info {
            flex: 1;
        }

        .chatbot-info h4 {
            margin: 0 0 var(--space-xs);
            font-size: var(--font-size-base);
        }

        .status {
            font-size: var(--font-size-xs);
            opacity: 0.9;
            margin: 0;
        }

        .status.online::before {
            content: '●';
            color: #10b981;
            margin-right: var(--space-xs);
        }

        .chatbot-minimize {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: var(--space-xs);
            border-radius: var(--radius-base);
            transition: background-color var(--transition-base);
        }

        .chatbot-minimize:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .chatbot-messages-demo {
            flex: 1;
            padding: var(--space-lg);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: var(--space-md);
            background: var(--background);
        }

        .message {
            display: flex;
            gap: var(--space-sm);
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
            align-items: flex-start;
        }

        .message.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .message.user-message {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .bot-message .message-avatar {
            background: var(--gradient-primary);
            color: white;
        }

        .user-message .message-avatar {
            background: var(--gradient-secondary);
            color: white;
        }

        .message-content {
            max-width: 80%;
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-lg);
            font-size: var(--font-size-sm);
            line-height: 1.4;
        }

        .bot-message .message-content {
            background: var(--background-light);
            color: var(--text-primary);
            border-bottom-left-radius: var(--radius-sm);
        }

        .user-message .message-content {
            background: var(--primary-color);
            color: white;
            border-bottom-right-radius: var(--radius-sm);
        }

        .message-time {
            font-size: var(--font-size-xs);
            color: var(--text-light);
            margin-top: var(--space-xs);
            align-self: flex-end;
        }

        .user-message .message-time {
            align-self: flex-start;
        }

        .typing-dots {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .typing-dots span {
            width: 6px;
            height: 6px;
            background: var(--text-secondary);
            border-radius: 50%;
            animation: typingDots 1.4s infinite ease-in-out both;
        }

        .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .typing-dots span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes typingDots {
            0%, 80%, 100% {
                transform: scale(0);
            }
            40% {
                transform: scale(1);
            }
        }

        .chatbot-input-demo {
            display: flex;
            padding: var(--space-lg);
            border-top: 1px solid var(--border-color);
            gap: var(--space-sm);
            background: var(--background);
        }

        .chatbot-input-demo input {
            flex: 1;
            padding: var(--space-sm);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            background: var(--background);
            color: var(--text-primary);
            font-size: var(--font-size-sm);
        }

        .chatbot-input-demo input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .chatbot-input-demo button {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color var(--transition-base);
        }

        .chatbot-input-demo button:hover {
            background: var(--primary-dark);
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--space-xl);
        }

        .feature-card {
            padding: var(--space-xl);
            text-align: center;
            transition: all var(--transition-base);
        }

        .feature-icon-large {
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

        .feature-card h3 {
            margin-bottom: var(--space-sm);
            color: var(--text-primary);
        }

        .feature-card p {
            margin-bottom: var(--space-md);
            color: var(--text-secondary);
        }

        .feature-benefits {
            list-style: none;
            padding: 0;
            text-align: left;
        }

        .feature-benefits li {
            padding: var(--space-xs) 0;
            color: var(--text-secondary);
            position: relative;
            padding-left: var(--space-md);
        }

        .feature-benefits li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--secondary-color);
            font-weight: bold;
        }

        /* Process Timeline */
        .process-timeline {
            display: flex;
            flex-direction: column;
            gap: var(--space-xl);
            max-width: 800px;
            margin: 0 auto;
        }

        .process-step {
            display: flex;
            align-items: flex-start;
            gap: var(--space-lg);
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: var(--gradient-primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-size-xl);
            font-weight: var(--font-weight-bold);
            flex-shrink: 0;
        }

        .step-content {
            flex: 1;
            padding: var(--space-lg);
        }

        .step-content h3 {
            margin-bottom: var(--space-sm);
            color: var(--text-primary);
        }

        .step-content p {
            margin-bottom: var(--space-md);
            color: var(--text-secondary);
        }

        .step-details {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-sm);
        }

        .detail-item {
            padding: var(--space-xs) var(--space-sm);
            background: var(--background-light);
            border-radius: var(--radius-full);
            font-size: var(--font-size-xs);
            color: var(--text-secondary);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .demo-content {
                grid-template-columns: 1fr;
                gap: var(--space-xl);
            }

            .demo-chatbot {
                position: static;
                order: -1;
            }

            .chatbot-container {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .features-grid {
                grid-template-columns: 1fr;
            }

            .process-step {
                flex-direction: column;
                text-align: center;
            }

            .step-details {
                justify-content: center;
            }

            .chatbot-container {
                height: 500px;
            }
        }

        @media (max-width: 480px) {
            .suggestion-buttons {
                gap: var(--space-xs);
            }

            .suggestion-btn {
                padding: var(--space-xs) var(--space-sm);
                font-size: var(--font-size-xs);
            }

            .chatbot-container {
                height: 400px;
            }
        }
    </style>
`;

// Initialize demo when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Add styles to head
    document.head.insertAdjacentHTML('beforeend', demoStyles);
    
    // Initialize demo chatbot
    window.chatbotDemo = new ChatbotDemo();
    
    console.log('🎭 Chatbot demo ready');
});

// Export for external use
window.ChatbotDemo = ChatbotDemo;