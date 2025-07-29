// 2GK - JavaScript principal
// Fonctionnalités interactives du site

class TwoGK {
    constructor() {
        this.cart = this.loadCart();
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateCartDisplay();
        this.setupSearch();
        this.setupFilters();
    }

    setupEventListeners() {
        // Boutons d'ajout au panier
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart')) {
                e.preventDefault();
                this.addToCart(e.target);
            }
            
            if (e.target.classList.contains('remove-from-cart')) {
                e.preventDefault();
                this.removeFromCart(e.target.dataset.productId);
            }
            
            if (e.target.classList.contains('update-quantity')) {
                e.preventDefault();
                this.updateQuantity(e.target.dataset.productId, e.target.value);
            }
        });

        // Navigation mobile
        const mobileToggle = document.querySelector('.mobile-toggle');
        if (mobileToggle) {
            mobileToggle.addEventListener('click', this.toggleMobileMenu);
        }

        // Formulaires
        const forms = document.querySelectorAll('form[data-ajax]');
        forms.forEach(form => {
            form.addEventListener('submit', this.handleAjaxForm.bind(this));
        });
    }

    // Gestion du panier
    addToCart(button) {
        const productId = button.dataset.productId;
        const productName = button.dataset.productName;
        const productPrice = parseFloat(button.dataset.productPrice);
        const maxQuantity = parseInt(button.dataset.maxQuantity) || 1;

        // Vérifier si l'utilisateur est connecté
        if (!this.isUserLoggedIn()) {
            this.showLoginModal();
            return;
        }

        // Vérifier si le produit est déjà dans le panier
        const existingItem = this.cart.find(item => item.id === productId);
        
        if (existingItem) {
            if (existingItem.quantity < maxQuantity) {
                existingItem.quantity++;
                this.showAlert('Quantité mise à jour dans le panier', 'success');
            } else {
                this.showAlert('Stock insuffisant', 'warning');
                return;
            }
        } else {
            this.cart.push({
                id: productId,
                name: productName,
                price: productPrice,
                quantity: 1,
                maxQuantity: maxQuantity
            });
            this.showAlert('Produit ajouté au panier', 'success');
        }

        this.saveCart();
        this.updateCartDisplay();
        this.sendCartToServer();
    }

    removeFromCart(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.saveCart();
        this.updateCartDisplay();
        this.sendCartToServer();
        this.showAlert('Produit retiré du panier', 'info');
    }

    updateQuantity(productId, newQuantity) {
        const item = this.cart.find(item => item.id === productId);
        if (item) {
            const quantity = parseInt(newQuantity);
            if (quantity > 0 && quantity <= item.maxQuantity) {
                item.quantity = quantity;
                this.saveCart();
                this.updateCartDisplay();
                this.sendCartToServer();
            } else if (quantity > item.maxQuantity) {
                this.showAlert('Stock insuffisant', 'warning');
            }
        }
    }

    updateCartDisplay() {
        const cartCount = document.querySelector('.cart-count');
        const cartTotal = document.querySelector('.cart-total');
        
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        const totalPrice = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        if (cartCount) {
            cartCount.textContent = totalItems;
            cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
        }

        if (cartTotal) {
            cartTotal.textContent = this.formatPrice(totalPrice);
        }

        // Mettre à jour la page panier si elle est ouverte
        this.updateCartPage();
    }

    updateCartPage() {
        const cartContainer = document.querySelector('.cart-items');
        if (!cartContainer) return;

        if (this.cart.length === 0) {
            cartContainer.innerHTML = '<p class="text-center">Votre panier est vide</p>';
            return;
        }

        let html = '';
        this.cart.forEach(item => {
            html += `
                <div class="cart-item" data-product-id="${item.id}">
                    <div class="cart-item-info">
                        <h4>${item.name}</h4>
                        <p class="price">${this.formatPrice(item.price)}</p>
                    </div>
                    <div class="cart-item-controls">
                        <input type="number" min="1" max="${item.maxQuantity}" 
                               value="${item.quantity}" class="update-quantity" 
                               data-product-id="${item.id}">
                        <button class="btn btn-danger remove-from-cart" 
                                data-product-id="${item.id}">Supprimer</button>
                    </div>
                    <div class="cart-item-total">
                        ${this.formatPrice(item.price * item.quantity)}
                    </div>
                </div>
            `;
        });

        cartContainer.innerHTML = html;
    }

    loadCart() {
        const saved = localStorage.getItem('2gk_cart');
        return saved ? JSON.parse(saved) : [];
    }

    saveCart() {
        localStorage.setItem('2gk_cart', JSON.stringify(this.cart));
    }

    sendCartToServer() {
        if (this.isUserLoggedIn()) {
            fetch('/api/cart/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(this.cart)
            }).catch(error => {
                console.error('Erreur de synchronisation du panier:', error);
            });
        }
    }

    isUserLoggedIn() {
        return document.body.classList.contains('user-logged-in') || 
               sessionStorage.getItem('user_logged_in') === 'true';
    }

    // Recherche
    setupSearch() {
        const searchInput = document.querySelector('.search-input');
        const searchBtn = document.querySelector('.search-btn');
        
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.performSearch(e.target.value);
                }, 300);
            });
        }

        if (searchBtn) {
            searchBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const query = searchInput.value;
                this.performSearch(query);
            });
        }
    }

    performSearch(query) {
        if (query.length < 2) return;

        const productsGrid = document.querySelector('.products-grid');
        if (!productsGrid) return;

        // Afficher le loader
        this.showLoader(productsGrid);

        fetch(`/api/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                this.displaySearchResults(data.products);
            })
            .catch(error => {
                console.error('Erreur de recherche:', error);
                this.showAlert('Erreur lors de la recherche', 'danger');
            });
    }

    displaySearchResults(products) {
        const productsGrid = document.querySelector('.products-grid');
        if (!productsGrid) return;

        if (products.length === 0) {
            productsGrid.innerHTML = '<p class="text-center">Aucun produit trouvé</p>';
            return;
        }

        let html = '';
        products.forEach(product => {
            html += this.createProductCard(product);
        });

        productsGrid.innerHTML = html;
    }

    // Filtres
    setupFilters() {
        const filterInputs = document.querySelectorAll('.filter-input');
        filterInputs.forEach(input => {
            input.addEventListener('change', this.applyFilters.bind(this));
        });
    }

    applyFilters() {
        const filters = {};
        const filterInputs = document.querySelectorAll('.filter-input');
        
        filterInputs.forEach(input => {
            if (input.value) {
                filters[input.name] = input.value;
            }
        });

        const queryString = new URLSearchParams(filters).toString();
        
        fetch(`/api/products?${queryString}`)
            .then(response => response.json())
            .then(data => {
                this.displaySearchResults(data.products);
            })
            .catch(error => {
                console.error('Erreur de filtrage:', error);
            });
    }

    // Utilitaires
    createProductCard(product) {
        const stockClass = product.stock > 10 ? 'stock-available' : 
                          product.stock > 0 ? 'stock-low' : 'stock-out';
        const stockText = product.stock > 0 ? `${product.stock} disponible(s)` : 'Rupture de stock';
        
        return `
            <div class="product-card">
                <div class="product-image">
                    ${product.image ? 
                        `<img src="${product.image}" alt="${product.nom}">` : 
                        '<i class="fas fa-image"></i>'
                    }
                </div>
                <div class="product-info">
                    <h3 class="product-title">${product.nom}</h3>
                    <p class="product-description">${product.description || ''}</p>
                    <div class="product-meta">
                        <span class="product-country">${product.pays || ''}</span>
                        <span class="product-platform">${product.plateforme || ''}</span>
                    </div>
                    <div class="product-price">${this.formatPrice(product.prix)}</div>
                    <div class="product-stock ${stockClass}">${stockText}</div>
                    <button class="btn btn-primary btn-full add-to-cart" 
                            data-product-id="${product.id}"
                            data-product-name="${product.nom}"
                            data-product-price="${product.prix}"
                            data-max-quantity="${product.stock}"
                            ${product.stock === 0 ? 'disabled' : ''}>
                        ${product.stock === 0 ? 'Rupture de stock' : 'Ajouter au panier'}
                    </button>
                </div>
            </div>
        `;
    }

    formatPrice(price) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'XOF'
        }).format(price);
    }

    showAlert(message, type = 'info') {
        const alertContainer = document.querySelector('.alert-container') || this.createAlertContainer();
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="alert-close">&times;</button>
        `;

        alertContainer.appendChild(alert);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);

        // Manual close
        alert.querySelector('.alert-close').addEventListener('click', () => {
            alert.remove();
        });
    }

    createAlertContainer() {
        const container = document.createElement('div');
        container.className = 'alert-container';
        container.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(container);
        return container;
    }

    showLoader(container) {
        container.innerHTML = '<div class="loader"></div>';
    }

    showLoginModal() {
        // Créer et afficher la modal de connexion
        const modal = document.createElement('div');
        modal.className = 'login-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Connexion requise</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Vous devez être connecté pour ajouter des produits au panier.</p>
                    <div class="modal-actions">
                        <a href="/login" class="btn btn-primary">Se connecter</a>
                        <a href="/register" class="btn btn-secondary">S'inscrire</a>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Fermer la modal
        modal.querySelector('.modal-close').addEventListener('click', () => {
            modal.remove();
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    toggleMobileMenu() {
        const navMenu = document.querySelector('.nav-menu');
        navMenu.classList.toggle('mobile-open');
    }

    handleAjaxForm(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const action = form.action;
        const method = form.method || 'POST';

        fetch(action, {
            method: method,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showAlert(data.message, 'success');
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                }
            } else {
                this.showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            this.showAlert('Une erreur est survenue', 'danger');
        });
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    window.twoGK = new TwoGK();
});

// Styles pour les modals et alertes
const styles = `
.login-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
}

.modal-content {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 2rem;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 1rem;
}

.modal-close {
    background: none;
    border: none;
    color: var(--text-color);
    font-size: 1.5rem;
    cursor: pointer;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.alert-close {
    background: none;
    border: none;
    color: inherit;
    font-size: 1.2rem;
    cursor: pointer;
    float: right;
    margin-left: 10px;
}

.cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.cart-item-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.cart-item-controls input {
    width: 60px;
    padding: 0.5rem;
    text-align: center;
}

@media (max-width: 768px) {
    .nav-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--primary-color);
        flex-direction: column;
        padding: 1rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
    
    .nav-menu.mobile-open {
        display: flex;
    }
    
    .mobile-toggle {
        display: block;
        background: none;
        border: none;
        color: var(--text-color);
        font-size: 1.5rem;
        cursor: pointer;
    }
}
`;

// Injecter les styles
const styleSheet = document.createElement('style');
styleSheet.textContent = styles;
document.head.appendChild(styleSheet);