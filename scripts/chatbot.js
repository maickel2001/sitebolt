/**
 * Chatbot functionality for MonSite.pro
 * Handles chat interface, message processing, and responses
 */

class Chatbot {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.responses = this.initializeResponses();
        this.currentContext = null;
        this.userName = null;
        
        // DOM elements
        this.toggle = document.getElementById('chatbot-toggle');
        this.window = document.getElementById('chatbot-window');
        this.messages_container = document.getElementById('chatbot-messages');
        this.input = document.getElementById('chatbot-input');
        this.sendButton = document.getElementById('chatbot-send');
        
        this.init();
    }

    init() {
        if (!this.toggle || !this.window) return;

        // Event listeners
        this.toggle.addEventListener('click', () => this.toggleChat());
        this.sendButton.addEventListener('click', () => this.sendMessage());
        this.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });

        // Initialize with welcome message
        this.addMessage('Bonjour ! Je suis votre assistant virtuel. Comment puis-je vous aider aujourd\'hui ?', 'bot');
        
        console.log('🤖 Chatbot initialized');
    }

    initializeResponses() {
        return {
            greetings: [
                'Bonjour ! Comment puis-je vous aider ?',
                'Salut ! Que puis-je faire pour vous ?',
                'Bonsoir ! En quoi puis-je vous être utile ?'
            ],
            
            services: {
                keywords: ['service', 'services', 'que faites-vous', 'que fais-tu', 'prestations', 'offre'],
                responses: [
                    `Je propose plusieurs services :\n\n💼 **Comptabilité & Fiscalité**\n- Tenue de comptabilité\n- Déclarations fiscales\n- Conseil et audit\n\n💻 **Développement Web**\n- Sites web vitrine\n- Applications web\n- Maintenance et support\n\n🎯 **Conseil Digital**\n- Audit digital\n- Formation\n- Stratégie digitale\n\nSouhaitez-vous en savoir plus sur un service en particulier ?`
                ]
            },
            
            comptabilite: {
                keywords: ['comptabilité', 'comptable', 'compta', 'fiscal', 'fiscalité', 'déclaration', 'tva', 'bilan'],
                responses: [
                    `🧮 **Services Comptables :**\n\n📊 **Tenue de comptabilité**\n- Saisie comptable en temps réel\n- Lettrage et rapprochements\n- Bilan et compte de résultat\n- À partir de 150€/mois\n\n📋 **Déclarations fiscales**\n- TVA (mensuelle/trimestrielle)\n- Impôt sur les sociétés\n- CFE, CVAE\n- À partir de 80€/déclaration\n\n💡 **Conseil & Audit**\n- Optimisation fiscale\n- Audit des comptes\n- Conseil en gestion\n\nVoulez-vous un devis personnalisé ?`
                ]
            },
            
            developpement: {
                keywords: ['développement', 'site web', 'site', 'application', 'web', 'digital', 'numérique'],
                responses: [
                    `💻 **Services de Développement Web :**\n\n🌐 **Sites web vitrine**\n- Design responsive moderne\n- Optimisation SEO\n- CMS intégré\n- À partir de 1500€\n\n⚡ **Applications web**\n- Développement sur mesure\n- Interface intuitive\n- API sécurisées\n- À partir de 3000€\n\n🔧 **Maintenance & Support**\n- Monitoring 24/7\n- Sauvegardes automatiques\n- Support technique\n- À partir de 100€/mois\n\nQuel type de projet avez-vous en tête ?`
                ]
            },
            
            tarifs: {
                keywords: ['prix', 'tarif', 'tarifs', 'coût', 'combien', 'devis'],
                responses: [
                    `💰 **Tarifs Indicatifs :**\n\n**Comptabilité :**\n• Tenue comptable : 150€/mois\n• Déclarations TVA : 80€\n• Bilan annuel : 500€\n\n**Développement :**\n• Site vitrine : 1500€+\n• Application web : 3000€+\n• Maintenance : 100€/mois\n\n**Conseil :**\n• Audit digital : 500€\n• Formation : 200€/jour\n• Stratégie : sur devis\n\n📞 Contactez-moi pour un devis personnalisé gratuit !`
                ]
            },
            
            contact: {
                keywords: ['contact', 'contacter', 'rdv', 'rendez-vous', 'téléphone', 'email', 'adresse'],
                responses: [
                    `📞 **Me Contacter :**\n\n📧 **Email :** contact@monsite.pro\n📱 **Téléphone :** +33 1 23 45 67 89\n📍 **Adresse :** Paris, France\n\n🕒 **Disponibilités :**\nLundi - Vendredi : 9h - 18h\nSamedi : 9h - 12h (sur RDV)\n\n💬 Vous pouvez aussi utiliser le formulaire de contact sur le site pour me laisser un message détaillé !`
                ]
            },
            
            about: {
                keywords: ['qui êtes-vous', 'qui es-tu', 'présentation', 'parcours', 'expérience', 'à propos'],
                responses: [
                    `👨‍💼 **À propos de moi :**\n\nJe suis un expert-comptable ET développeur web avec plus de 8 ans d'expérience.\n\n🎓 **Double expertise :**\n• Expert-comptable diplômé\n• Développeur full-stack passionné\n• Spécialisé dans la transformation digitale\n\n📈 **Mes chiffres :**\n• 150+ clients satisfaits\n• 200+ projets livrés\n• 98% de satisfaction client\n\nMa mission : allier rigueur comptable et innovation technologique pour optimiser votre entreprise !`
                ]
            },
            
            horaires: {
                keywords: ['horaire', 'horaires', 'ouvert', 'disponible', 'quand'],
                responses: [
                    `🕒 **Mes Horaires :**\n\n**Lundi - Vendredi :**\n9h00 - 12h30\n14h00 - 18h00\n\n**Samedi :**\n9h00 - 12h00 (sur rendez-vous)\n\n**Dimanche :** Fermé\n\n⚡ **Urgences comptables :**\nDisponible 7j/7 pour les déclarations urgentes\n\n📱 **Support technique :**\nMonitoring 24/7 pour les sites en maintenance`
                ]
            },
            
            formations: {
                keywords: ['formation', 'apprendre', 'cours', 'enseigner'],
                responses: [
                    `🎓 **Formations Proposées :**\n\n**Pour les entreprises :**\n• Utilisation des outils comptables\n• Gestion digitale des documents\n• Optimisation des processus\n\n**Pour les développeurs :**\n• Intégration comptable dans les apps\n• APIs de facturation\n• Conformité RGPD\n\n💡 **Format :**\n• Présentiel ou distanciel\n• Support pédagogique inclus\n• Suivi post-formation\n\n💰 **Tarif :** 200€/jour\n\nIntéressé(e) par une formation ?`
                ]
            },
            
            default: [
                'Je ne suis pas sûr de comprendre. Pouvez-vous reformuler votre question ?',
                'Désolé, je n\'ai pas bien saisi. Pourriez-vous être plus précis ?',
                'Hmm, je n\'ai pas la réponse à cette question. Contactez-moi directement pour plus d\'informations !',
                'Cette question dépasse mes compétences actuelles. N\'hésitez pas à me contacter par email ou téléphone.'
            ],
            
            thanks: {
                keywords: ['merci', 'thanks', 'remercie', 'parfait', 'super', 'génial', 'excellent'],
                responses: [
                    'Je vous en prie ! N\'hésitez pas si vous avez d\'autres questions.',
                    'Avec plaisir ! Je suis là pour vous aider.',
                    'Ravi d\'avoir pu vous aider ! À bientôt.',
                    'De rien ! N\'hésitez pas à revenir vers moi si besoin.'
                ]
            },
            
            bye: {
                keywords: ['au revoir', 'bye', 'à bientôt', 'salut', 'ciao', 'goodbye'],
                responses: [
                    'Au revoir ! N\'hésitez pas à revenir si vous avez des questions.',
                    'À bientôt ! Bonne journée.',
                    'Au plaisir de vous revoir ! Portez-vous bien.',
                    'Excellente journée ! À très vite.'
                ]
            }
        };
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        this.toggle.classList.toggle('active', this.isOpen);
        this.window.classList.toggle('active', this.isOpen);
        
        if (this.isOpen) {
            this.input.focus();
        }
    }

    sendMessage() {
        const message = this.input.value.trim();
        if (!message) return;

        // Add user message
        this.addMessage(message, 'user');
        
        // Clear input
        this.input.value = '';
        
        // Show typing indicator
        this.showTypingIndicator();
        
        // Process and respond
        setTimeout(() => {
            this.hideTypingIndicator();
            const response = this.processMessage(message);
            this.addMessage(response, 'bot');
        }, 1000 + Math.random() * 1000); // Simulate thinking time
    }

    addMessage(content, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        
        // Convert markdown-style formatting to HTML
        const formattedContent = this.formatMessage(content);
        messageContent.innerHTML = formattedContent;
        
        messageDiv.appendChild(messageContent);
        this.messages_container.appendChild(messageDiv);
        
        // Scroll to bottom
        this.messages_container.scrollTop = this.messages_container.scrollHeight;
        
        // Store message
        this.messages.push({ content, sender, timestamp: new Date() });
    }

    formatMessage(message) {
        return message
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Bold
            .replace(/\*(.*?)\*/g, '<em>$1</em>') // Italic
            .replace(/\n/g, '<br>'); // Line breaks
    }

    showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot-message typing-indicator';
        typingDiv.innerHTML = `
            <div class="message-content">
                <div class="loading-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        `;
        this.messages_container.appendChild(typingDiv);
        this.messages_container.scrollTop = this.messages_container.scrollHeight;
    }

    hideTypingIndicator() {
        const typingIndicator = this.messages_container.querySelector('.typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    processMessage(message) {
        const normalizedMessage = message.toLowerCase().trim();
        
        // Check for specific keywords and contexts
        for (const [category, data] of Object.entries(this.responses)) {
            if (category === 'greetings' || category === 'default') continue;
            
            if (data.keywords) {
                const hasKeyword = data.keywords.some(keyword => 
                    normalizedMessage.includes(keyword.toLowerCase())
                );
                
                if (hasKeyword) {
                    this.currentContext = category;
                    return this.getRandomResponse(data.responses);
                }
            }
        }
        
        // Check for greetings
        const greetingKeywords = ['bonjour', 'salut', 'hello', 'coucou', 'bonsoir', 'bonne', 'hi'];
        if (greetingKeywords.some(keyword => normalizedMessage.includes(keyword))) {
            return this.getRandomResponse(this.responses.greetings);
        }
        
        // Check for thanks
        if (this.responses.thanks.keywords.some(keyword => normalizedMessage.includes(keyword))) {
            return this.getRandomResponse(this.responses.thanks.responses);
        }
        
        // Check for goodbye
        if (this.responses.bye.keywords.some(keyword => normalizedMessage.includes(keyword))) {
            return this.getRandomResponse(this.responses.bye.responses);
        }
        
        // Default response with helpful suggestions
        const suggestions = [
            'Vous pouvez me demander des informations sur :',
            '• Mes services (comptabilité, développement web)',
            '• Mes tarifs et devis',
            '• Comment me contacter',
            '• Mes horaires de disponibilité',
            '• Mon parcours professionnel'
        ];
        
        return this.getRandomResponse(this.responses.default) + '\n\n' + suggestions.join('\n');
    }

    getRandomResponse(responses) {
        return responses[Math.floor(Math.random() * responses.length)];
    }

    // Public methods for external integration
    openChat() {
        if (!this.isOpen) {
            this.toggleChat();
        }
    }

    closeChat() {
        if (this.isOpen) {
            this.toggleChat();
        }
    }

    sendProgrammaticMessage(message) {
        this.addMessage(message, 'bot');
    }

    clearChat() {
        this.messages_container.innerHTML = `
            <div class="message bot-message">
                <div class="message-content">
                    Bonjour ! Comment puis-je vous aider aujourd'hui ?
                </div>
            </div>
        `;
        this.messages = [];
        this.currentContext = null;
    }

    // Analytics and insights
    getConversationInsights() {
        const totalMessages = this.messages.length;
        const userMessages = this.messages.filter(m => m.sender === 'user').length;
        const botMessages = this.messages.filter(m => m.sender === 'bot').length;
        
        return {
            totalMessages,
            userMessages,
            botMessages,
            averageResponseTime: this.calculateAverageResponseTime(),
            mostDiscussedTopics: this.getMostDiscussedTopics()
        };
    }

    calculateAverageResponseTime() {
        // Simulate response time calculation
        return '1.2s';
    }

    getMostDiscussedTopics() {
        const topics = {};
        this.messages
            .filter(m => m.sender === 'user')
            .forEach(message => {
                const content = message.content.toLowerCase();
                Object.keys(this.responses).forEach(topic => {
                    if (this.responses[topic].keywords) {
                        const hasKeyword = this.responses[topic].keywords.some(keyword =>
                            content.includes(keyword.toLowerCase())
                        );
                        if (hasKeyword) {
                            topics[topic] = (topics[topic] || 0) + 1;
                        }
                    }
                });
            });
        
        return Object.entries(topics)
            .sort(([,a], [,b]) => b - a)
            .slice(0, 3)
            .map(([topic]) => topic);
    }
}

// Advanced chatbot features
class ChatbotAnalytics {
    constructor(chatbot) {
        this.chatbot = chatbot;
        this.sessionStart = new Date();
        this.interactions = [];
    }

    trackInteraction(type, data) {
        this.interactions.push({
            type,
            data,
            timestamp: new Date()
        });
    }

    getSessionData() {
        return {
            sessionDuration: new Date() - this.sessionStart,
            totalInteractions: this.interactions.length,
            messagesExchanged: this.chatbot.messages.length,
            topicsCovered: this.chatbot.getMostDiscussedTopics()
        };
    }
}

// Initialize chatbot when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.chatbot = new Chatbot();
    window.chatbotAnalytics = new ChatbotAnalytics(window.chatbot);
    
    // Add some smart behaviors
    
    // Auto-open chat on specific pages
    const currentPage = window.location.pathname;
    if (currentPage.includes('contact') || currentPage.includes('services')) {
        setTimeout(() => {
            window.chatbot.sendProgrammaticMessage(
                'Je vois que vous consultez ma page de services ! N\'hésitez pas si vous avez des questions spécifiques. 😊'
            );
        }, 3000);
    }
    
    // Welcome back returning visitors
    if (localStorage.getItem('chatbot-visited')) {
        setTimeout(() => {
            window.chatbot.sendProgrammaticMessage(
                'Ravi de vous revoir ! Comment puis-je vous aider aujourd\'hui ?'
            );
        }, 2000);
    } else {
        localStorage.setItem('chatbot-visited', 'true');
    }
    
    console.log('🤖 Chatbot system ready');
});

// Export for external use
window.Chatbot = Chatbot;
window.ChatbotAnalytics = ChatbotAnalytics;