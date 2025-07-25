/**
 * Portfolio functionality
 * Handles filtering, modal display and project showcase
 */

class PortfolioManager {
    constructor() {
        this.filterButtons = document.querySelectorAll('.filter-btn');
        this.portfolioItems = document.querySelectorAll('.portfolio-item');
        this.modal = document.getElementById('portfolio-modal');
        this.modalBody = document.getElementById('modal-body');
        this.currentFilter = 'all';
        
        this.projectData = this.initializeProjectData();
        this.init();
    }

    init() {
        this.setupFilters();
        this.setupModal();
        this.setupLazyLoading();
        console.log('📁 Portfolio manager initialized');
    }

    setupFilters() {
        this.filterButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const filter = e.target.getAttribute('data-filter');
                this.filterProjects(filter);
                this.updateActiveFilter(button);
            });
        });
    }

    filterProjects(filter) {
        this.currentFilter = filter;
        
        this.portfolioItems.forEach(item => {
            const itemCategory = item.getAttribute('data-category');
            const shouldShow = filter === 'all' || itemCategory === filter;
            
            if (shouldShow) {
                item.style.display = 'block';
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 50);
            } else {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    item.style.display = 'none';
                }, 300);
            }
        });
    }

    updateActiveFilter(activeButton) {
        this.filterButtons.forEach(btn => btn.classList.remove('active'));
        activeButton.classList.add('active');
    }

    setupModal() {
        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.classList.contains('active')) {
                this.closeModal();
            }
        });
    }

    setupLazyLoading() {
        const images = document.querySelectorAll('.portfolio-image img');
        
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.src; // Trigger loading if not already loaded
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    initializeProjectData() {
        return {
            techstore: {
                title: 'Plateforme E-commerce TechStore',
                category: 'Développement Web',
                client: 'TechStore SAS',
                duration: '3 mois',
                year: '2024',
                image: 'https://images.pexels.com/photos/196644/pexels-photo-196644.jpeg?auto=compress&cs=tinysrgb&w=800',
                description: 'Développement complet d\'une plateforme e-commerce moderne pour une entreprise spécialisée dans la vente de matériel informatique. Le projet incluait la création d\'un site web responsive, d\'un système de gestion des commandes, d\'un back-office administrateur et d\'une intégration complète avec les systèmes de paiement.',
                challenges: [
                    'Gestion de plus de 10 000 produits avec variations',
                    'Intégration avec multiple systèmes de paiement',
                    'Optimisation des performances pour un fort trafic',
                    'Synchronisation avec le système ERP existant'
                ],
                solutions: [
                    'Architecture microservices pour la scalabilité',
                    'Cache Redis pour les performances',
                    'API REST sécurisées pour l\'intégration',
                    'Tests automatisés complets'
                ],
                technologies: ['React', 'Node.js', 'MongoDB', 'Stripe', 'Redis', 'Docker'],
                results: [
                    '+150% d\'augmentation des ventes en ligne',
                    '-40% de temps de chargement des pages',
                    '99.9% de disponibilité du service',
                    'Retour sur investissement en 6 mois'
                ],
                testimonial: {
                    text: 'Un travail exceptionnel ! Notre chiffre d\'affaires en ligne a explosé grâce à cette nouvelle plateforme. L\'équipe a été très professionnelle et à l\'écoute.',
                    author: 'Marie Dubois, Directrice TechStore'
                }
            },
            medical: {
                title: 'Site Vitrine Cabinet Médical',
                category: 'Développement Web',
                client: 'Cabinet Dr. Martin',
                duration: '6 semaines',
                year: '2024',
                image: 'https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=800',
                description: 'Création d\'un site vitrine professionnel pour un cabinet médical avec système de prise de rendez-vous en ligne, espace patient sécurisé et présentation des services médicaux.',
                challenges: [
                    'Respect strict des normes RGPD dans le secteur médical',
                    'Interface intuitive pour tous les âges',
                    'Système de RDV synchronisé avec l\'agenda médical',
                    'Sécurisation des données patients'
                ],
                solutions: [
                    'Chiffrement SSL/TLS renforcé',
                    'Interface adaptative et accessible',
                    'Intégration avec le logiciel médical',
                    'Sauvegardes automatiques chiffrées'
                ],
                technologies: ['HTML5', 'CSS3', 'JavaScript', 'PHP', 'MySQL', 'SSL'],
                results: [
                    '+80% de rendez-vous pris en ligne',
                    '-50% d\'appels téléphoniques pour les RDV',
                    '95% de satisfaction patients',
                    'Gain de temps de 2h/jour pour le secrétariat'
                ],
                testimonial: {
                    text: 'Le site a révolutionné notre organisation. Les patients adorent pouvoir prendre RDV en ligne 24h/24. C\'est un vrai plus pour notre cabinet.',
                    author: 'Dr. Martin, Médecin généraliste'
                }
            },
            restaurant: {
                title: 'Application de Gestion Restaurant',
                category: 'Développement Web',
                client: 'Restaurant Le Gourmet',
                duration: '4 mois',
                year: '2023',
                image: 'https://images.pexels.com/photos/3184360/pexels-photo-3184360.jpeg?auto=compress&cs=tinysrgb&w=800',
                description: 'Développement d\'une application web complète pour la gestion d\'un restaurant : gestion des commandes, du stock, du personnel et intégration comptable.',
                challenges: [
                    'Gestion en temps réel des commandes multiples',
                    'Synchronisation cuisine/salle/caisse',
                    'Gestion complexe des stocks et approvisionnements',
                    'Intégration comptable automatisée'
                ],
                solutions: [
                    'Architecture temps réel avec WebSockets',
                    'Interface tactile optimisée',
                    'Alertes automatiques pour les stocks',
                    'Export automatique vers la comptabilité'
                ],
                technologies: ['Vue.js', 'Express', 'PostgreSQL', 'Socket.io', 'PWA', 'Charts.js'],
                results: [
                    '-40% de temps de gestion quotidienne',
                    '+25% d\'efficacité en cuisine',
                    '0% de rupture de stock depuis la mise en place',
                    'Comptabilité mise à jour en temps réel'
                ],
                testimonial: {
                    text: 'Cette application a transformé notre restaurant. Nous gagnons un temps précieux et nous avons une vision claire de notre activité en temps réel.',
                    author: 'Pierre Lenoir, Gérant Restaurant Le Gourmet'
                }
            },
            startup: {
                title: 'Restructuration Comptable StartupTech',
                category: 'Comptabilité',
                client: 'StartupTech SARL',
                duration: '2 mois',
                year: '2024',
                image: 'https://images.pexels.com/photos/6289028/pexels-photo-6289028.jpeg?auto=compress&cs=tinysrgb&w=800',
                description: 'Mise en place complète de la comptabilité d\'une startup technologique : restructuration des processus, digitalisation et optimisation fiscale.',
                challenges: [
                    'Comptabilité inexistante depuis 2 ans',
                    'Nombreuses transactions en devises étrangères',
                    'Optimisation fiscale pour une croissance rapide',
                    'Besoin de reporting en temps réel pour les investisseurs'
                ],
                solutions: [
                    'Reconstitution complète des écritures',
                    'Automatisation des imports bancaires',
                    'Mise en place du CIR et autres dispositifs',
                    'Tableaux de bord automatisés'
                ],
                technologies: ['Sage', 'Excel', 'API Bancaires', 'Power BI', 'Python'],
                results: [
                    '-60% de temps de saisie comptable',
                    '€50k d\'économies fiscales la première année',
                    'Reporting investisseurs automatisé',
                    'Conformité fiscale à 100%'
                ],
                testimonial: {
                    text: 'Grâce à cette restructuration, nous avons pu lever des fonds sereinement. La comptabilité n\'est plus un frein mais un atout pour notre croissance.',
                    author: 'Thomas Durand, CEO StartupTech'
                }
            },
            pme: {
                title: 'Optimisation Fiscale PME',
                category: 'Comptabilité',
                client: 'Industrie Moderne SA',
                duration: '3 mois',
                year: '2023',
                image: 'https://images.pexels.com/photos/6863515/pexels-photo-6863515.jpeg?auto=compress&cs=tinysrgb&w=800',
                description: 'Audit complet et restructuration fiscale d\'une PME industrielle de 50 salariés avec optimisation des charges sociales et fiscales.',
                challenges: [
                    'Charges fiscales et sociales très élevées',
                    'Méconnaissance des dispositifs d\'aide',
                    'Gestion complexe de la TVA intracommunautaire',
                    'Optimisation de la transmission d\'entreprise'
                ],
                solutions: [
                    'Audit fiscal complet sur 3 exercices',
                    'Mise en place de tous les crédits d\'impôt éligibles',
                    'Restructuration juridique optimale',
                    'Plan de transmission sur 10 ans'
                ],
                technologies: ['Audit', 'CIR/CII', 'Optimisation sociale', 'TVA', 'Transmission'],
                results: [
                    '-25% d\'impôts sur les sociétés',
                    '€120k de crédits d\'impôt récupérés',
                    '-15% de charges sociales',
                    'Plan de transmission fiscalement optimisé'
                ],
                testimonial: {
                    text: 'Les économies réalisées nous permettent d\'investir davantage dans notre développement. Un accompagnement de très haute qualité.',
                    author: 'Jean-Claude Moreau, Directeur Général'
                }
            },
            avocats: {
                title: 'Transformation Digitale Cabinet Avocats',
                category: 'Conseil Digital',
                client: 'Cabinet Juridique & Associés',
                duration: '6 mois',
                year: '2024',
                image: 'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=800',
                description: 'Accompagnement complet dans la digitalisation d\'un cabinet d\'avocats : dématérialisation, outils collaboratifs et CRM juridique.',
                challenges: [
                    'Résistance au changement des équipes',
                    'Confidentialité absolue des dossiers clients',
                    'Intégration avec les tribunaux dématérialisés',
                    'Formation de 15 collaborateurs'
                ],
                solutions: [
                    'Conduite du changement progressive',
                    'Chiffrement de bout en bout',
                    'API sécurisées avec les greffes',
                    'Formation personnalisée par métier'
                ],
                technologies: ['CRM Juridique', 'GED', 'Workflow', 'Signature électronique', 'Formation'],
                results: [
                    '+50% de productivité globale',
                    '-80% de papier utilisé',
                    '100% des procédures dématérialisées',
                    'ROI atteint en 8 mois'
                ],
                testimonial: {
                    text: 'La transformation a été remarquable. Nous sommes passés d\'un cabinet traditionnel à un cabinet 4.0 en quelques mois. Nos clients apprécient cette modernité.',
                    author: 'Maître Sophie Leroy, Associée'
                }
            },
            ecommerce: {
                title: 'Audit Digital E-commerce',
                category: 'Conseil Digital',
                client: 'Boutique Mode Online',
                duration: '1 mois',
                year: '2024',
                image: 'https://images.pexels.com/photos/3184339/pexels-photo-3184339.jpeg?auto=compress&cs=tinysrgb&w=800',
                description: 'Audit complet de la présence digitale d\'une boutique de mode en ligne : SEO, UX, performance et stratégie marketing digital.',
                challenges: [
                    'Trafic en baisse constante depuis 6 mois',
                    'Taux de conversion très faible (0.8%)',
                    'Concurrence très agressive',
                    'Budget marketing limité'
                ],
                solutions: [
                    'Audit SEO technique complet',
                    'Refonte UX basée sur les données',
                    'Stratégie de contenu ciblée',
                    'Optimisation du tunnel de conversion'
                ],
                technologies: ['SEO', 'Google Analytics', 'UX/UI', 'A/B Testing', 'Performance'],
                results: [
                    '+200% de trafic organique',
                    '+150% de taux de conversion',
                    '-40% de coût d\'acquisition client',
                    'ROI marketing multiplié par 3'
                ],
                testimonial: {
                    text: 'L\'audit a révélé des problèmes que nous n\'avions pas identifiés. Les recommandations ont transformé notre business en quelques semaines.',
                    author: 'Camille Rousseau, E-commerce Manager'
                }
            }
        };
    }

    openModal(projectId) {
        const project = this.projectData[projectId];
        if (!project) return;

        this.modalBody.innerHTML = this.generateModalContent(project);
        this.modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Add animation
        setTimeout(() => {
            this.modal.querySelector('.modal-content').style.transform = 'scale(1)';
            this.modal.querySelector('.modal-content').style.opacity = '1';
        }, 10);
    }

    closeModal() {
        const modalContent = this.modal.querySelector('.modal-content');
        modalContent.style.transform = 'scale(0.9)';
        modalContent.style.opacity = '0';
        
        setTimeout(() => {
            this.modal.classList.remove('active');
            document.body.style.overflow = '';
        }, 300);
    }

    generateModalContent(project) {
        return `
            <div class="modal-header">
                <div class="modal-image">
                    <img src="${project.image}" alt="${project.title}" loading="lazy">
                </div>
                <div class="modal-info">
                    <div class="modal-category">${project.category}</div>
                    <h2>${project.title}</h2>
                    <div class="modal-meta">
                        <div class="meta-item">
                            <strong>Client:</strong> ${project.client}
                        </div>
                        <div class="meta-item">
                            <strong>Durée:</strong> ${project.duration}
                        </div>
                        <div class="meta-item">
                            <strong>Année:</strong> ${project.year}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-section">
                <h3>Description du projet</h3>
                <p>${project.description}</p>
            </div>
            
            <div class="modal-section">
                <h3>Défis rencontrés</h3>
                <ul class="challenge-list">
                    ${project.challenges.map(challenge => `<li>${challenge}</li>`).join('')}
                </ul>
            </div>
            
            <div class="modal-section">
                <h3>Solutions apportées</h3>
                <ul class="solution-list">
                    ${project.solutions.map(solution => `<li>${solution}</li>`).join('')}
                </ul>
            </div>
            
            <div class="modal-section">
                <h3>Technologies utilisées</h3>
                <div class="tech-tags">
                    ${project.technologies.map(tech => `<span class="tech-tag">${tech}</span>`).join('')}
                </div>
            </div>
            
            <div class="modal-section">
                <h3>Résultats obtenus</h3>
                <div class="results-grid">
                    ${project.results.map(result => `
                        <div class="result-item">
                            <div class="result-icon">✓</div>
                            <div class="result-text">${result}</div>
                        </div>
                    `).join('')}
                </div>
            </div>
            
            ${project.testimonial ? `
                <div class="modal-section">
                    <h3>Témoignage client</h3>
                    <div class="testimonial-card">
                        <blockquote>"${project.testimonial.text}"</blockquote>
                        <cite>— ${project.testimonial.author}</cite>
                    </div>
                </div>
            ` : ''}
            
            <div class="modal-actions">
                <a href="contact.html" class="btn btn-primary">
                    Démarrer un projet similaire
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </a>
                <button class="btn btn-outline" onclick="closePortfolioModal()">
                    Fermer
                </button>
            </div>
        `;
    }
}

// Global functions for modal control
function openPortfolioModal(projectId) {
    if (window.portfolioManager) {
        window.portfolioManager.openModal(projectId);
    }
}

function closePortfolioModal() {
    if (window.portfolioManager) {
        window.portfolioManager.closeModal();
    }
}

// Add portfolio-specific styles
const portfolioStyles = `
    <style>
        /* Portfolio Filter */
        .portfolio-filter {
            padding: var(--space-lg) 0;
            background: var(--background-light);
        }
        
        .filter-buttons {
            display: flex;
            justify-content: center;
            gap: var(--space-sm);
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: var(--space-sm) var(--space-lg);
            background: transparent;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-full);
            color: var(--text-secondary);
            font-weight: var(--font-weight-medium);
            cursor: pointer;
            transition: all var(--transition-base);
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Portfolio Grid */
        .portfolio-items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: var(--space-xl);
        }
        
        .portfolio-item {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
        .portfolio-card {
            padding: 0;
            overflow: hidden;
            transition: all var(--transition-base);
        }
        
        .portfolio-image {
            position: relative;
            height: 250px;
            overflow: hidden;
        }
        
        .portfolio-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform var(--transition-base);
        }
        
        .portfolio-card:hover .portfolio-image img {
            transform: scale(1.1);
        }
        
        .portfolio-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity var(--transition-base);
        }
        
        .portfolio-card:hover .portfolio-overlay {
            opacity: 1;
        }
        
        .portfolio-actions {
            display: flex;
            gap: var(--space-sm);
        }
        
        .btn-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition-base);
            text-decoration: none;
        }
        
        .btn-icon:hover {
            background: var(--primary-dark);
            transform: scale(1.1);
            color: white;
        }
        
        .portfolio-content {
            padding: var(--space-lg);
        }
        
        .portfolio-category {
            font-size: var(--font-size-sm);
            color: var(--primary-color);
            font-weight: var(--font-weight-semibold);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: var(--space-xs);
        }
        
        .portfolio-content h3 {
            margin-bottom: var(--space-sm);
            color: var(--text-primary);
        }
        
        .portfolio-content p {
            color: var(--text-secondary);
            margin-bottom: var(--space-md);
            line-height: 1.6;
        }
        
        .portfolio-tech {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-xs);
            margin-bottom: var(--space-md);
        }
        
        .tech-tag {
            padding: var(--space-xs) var(--space-sm);
            background: var(--primary-color);
            color: white;
            border-radius: var(--radius-sm);
            font-size: var(--font-size-xs);
            font-weight: var(--font-weight-medium);
        }
        
        .portfolio-stats {
            display: flex;
            justify-content: space-between;
            padding-top: var(--space-md);
            border-top: 1px solid var(--border-color);
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-value {
            display: block;
            font-size: var(--font-size-lg);
            font-weight: var(--font-weight-bold);
            color: var(--primary-color);
        }
        
        .stat-label {
            font-size: var(--font-size-sm);
            color: var(--text-secondary);
        }
        
        /* Portfolio Modal */
        .portfolio-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2000;
            display: none;
        }
        
        .portfolio-modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            position: relative;
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            background: var(--background);
            border-radius: var(--radius-xl);
            overflow-y: auto;
            transform: scale(0.9);
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .modal-close {
            position: absolute;
            top: var(--space-lg);
            right: var(--space-lg);
            width: 40px;
            height: 40px;
            background: var(--background);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1;
            transition: all var(--transition-base);
        }
        
        .modal-close:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .modal-header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-xl);
            padding: var(--space-xl);
            align-items: center;
        }
        
        .modal-image img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: var(--radius-lg);
        }
        
        .modal-category {
            font-size: var(--font-size-sm);
            color: var(--primary-color);
            font-weight: var(--font-weight-semibold);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: var(--space-xs);
        }
        
        .modal-info h2 {
            margin-bottom: var(--space-md);
            color: var(--text-primary);
        }
        
        .modal-meta {
            display: flex;
            flex-direction: column;
            gap: var(--space-xs);
        }
        
        .meta-item {
            color: var(--text-secondary);
        }
        
        .meta-item strong {
            color: var(--text-primary);
        }
        
        .modal-section {
            padding: 0 var(--space-xl) var(--space-xl);
        }
        
        .modal-section h3 {
            margin-bottom: var(--space-md);
            color: var(--text-primary);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: var(--space-xs);
            display: inline-block;
        }
        
        .challenge-list,
        .solution-list {
            list-style: none;
            padding: 0;
        }
        
        .challenge-list li,
        .solution-list li {
            padding: var(--space-sm) 0;
            padding-left: var(--space-lg);
            position: relative;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border-color);
        }
        
        .challenge-list li:before {
            content: '⚠️';
            position: absolute;
            left: 0;
        }
        
        .solution-list li:before {
            content: '💡';
            position: absolute;
            left: 0;
        }
        
        .tech-tags {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-sm);
        }
        
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-md);
        }
        
        .result-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-md);
            background: var(--background-light);
            border-radius: var(--radius-lg);
        }
        
        .result-icon {
            width: 30px;
            height: 30px;
            background: var(--secondary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .result-text {
            color: var(--text-primary);
            font-weight: var(--font-weight-medium);
        }
        
        .testimonial-card {
            background: var(--background-light);
            padding: var(--space-xl);
            border-radius: var(--radius-lg);
            border-left: 4px solid var(--primary-color);
        }
        
        .testimonial-card blockquote {
            font-size: var(--font-size-lg);
            font-style: italic;
            color: var(--text-primary);
            margin-bottom: var(--space-md);
            line-height: 1.6;
        }
        
        .testimonial-card cite {
            color: var(--text-secondary);
            font-weight: var(--font-weight-medium);
        }
        
        .modal-actions {
            padding: var(--space-xl);
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: var(--space-md);
            justify-content: center;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .portfolio-items {
                grid-template-columns: 1fr;
            }
            
            .modal-header {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .modal-content {
                width: 95%;
                max-height: 95vh;
            }
            
            .results-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-actions {
                flex-direction: column;
            }
        }
    </style>
`;

// Initialize portfolio when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Add styles to head
    document.head.insertAdjacentHTML('beforeend', portfolioStyles);
    
    // Initialize portfolio manager
    window.portfolioManager = new PortfolioManager();
    
    console.log('📁 Portfolio system ready');
});