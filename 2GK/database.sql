-- Base de données pour 2GK - Site de vente de produits numériques
-- Créé pour MySQL/MariaDB

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS 2gk_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE 2gk_database;

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_derniere_connexion TIMESTAMP NULL,
    statut ENUM('actif', 'suspendu', 'supprime') DEFAULT 'actif',
    verification_email BOOLEAN DEFAULT FALSE,
    token_verification VARCHAR(255) NULL
);

-- Table des catégories de produits
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    ordre_affichage INT DEFAULT 0,
    actif BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des produits
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    pays VARCHAR(100),
    plateforme VARCHAR(100),
    image VARCHAR(255),
    stock INT DEFAULT 0,
    type_livraison ENUM('automatique', 'manuelle') DEFAULT 'automatique',
    category_id INT,
    actif BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Table des codes de produits
CREATE TABLE codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    code TEXT NOT NULL,
    statut ENUM('disponible', 'vendu', 'reserve', 'expire') DEFAULT 'disponible',
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_utilisation TIMESTAMP NULL,
    order_id INT NULL,
    notes TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Table des commandes
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    numero_commande VARCHAR(50) UNIQUE NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    statut ENUM('en_attente', 'paye', 'livre', 'annule', 'rembourse') DEFAULT 'en_attente',
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_paiement TIMESTAMP NULL,
    date_livraison TIMESTAMP NULL,
    paiement_kia_id VARCHAR(255),
    adresse_facturation TEXT,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des articles de commande
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    code_id INT NULL,
    date_livraison TIMESTAMP NULL,
    statut_livraison ENUM('en_attente', 'livre', 'erreur') DEFAULT 'en_attente',
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (code_id) REFERENCES codes(id) ON DELETE SET NULL
);

-- Table des administrateurs
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'moderateur') DEFAULT 'admin',
    derniere_connexion TIMESTAMP NULL,
    actif BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des promotions et codes de réduction
CREATE TABLE promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('pourcentage', 'montant_fixe') NOT NULL,
    valeur DECIMAL(10,2) NOT NULL,
    montant_minimum DECIMAL(10,2) DEFAULT 0,
    utilisations_max INT DEFAULT NULL,
    utilisations_actuelles INT DEFAULT 0,
    date_debut TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_expiration TIMESTAMP NULL,
    actif BOOLEAN DEFAULT TRUE,
    description TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des utilisations de promotions
CREATE TABLE promotion_utilisations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    promotion_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT NOT NULL,
    date_utilisation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (promotion_id) REFERENCES promotions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Table des messages/support client
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    nom VARCHAR(100),
    email VARCHAR(255) NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    reponse TEXT NULL,
    statut ENUM('nouveau', 'en_cours', 'resolu', 'ferme') DEFAULT 'nouveau',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_reponse TIMESTAMP NULL,
    admin_id INT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE SET NULL
);

-- Table des paiements KiaPay
CREATE TABLE paiements_kia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    devise VARCHAR(3) DEFAULT 'XOF',
    reference_kia VARCHAR(255) UNIQUE NOT NULL,
    transaction_id VARCHAR(255),
    etat ENUM('en_attente', 'reussi', 'echec', 'annule', 'rembourse') DEFAULT 'en_attente',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_mise_a_jour TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    callback_data JSON,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Table des sessions de panier (pour les utilisateurs non connectés)
CREATE TABLE cart_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_expiration TIMESTAMP DEFAULT (CURRENT_TIMESTAMP + INTERVAL 24 HOUR),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Table des paniers utilisateurs (pour les utilisateurs connectés)
CREATE TABLE user_cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Table des logs d'activité admin
CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    table_affectee VARCHAR(100),
    id_enregistrement INT,
    anciennes_valeurs JSON,
    nouvelles_valeurs JSON,
    adresse_ip VARCHAR(45),
    user_agent TEXT,
    date_action TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE CASCADE
);

-- Table des paramètres du site
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cle VARCHAR(100) UNIQUE NOT NULL,
    valeur TEXT,
    description TEXT,
    type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertion des données par défaut

-- Catégories par défaut
INSERT INTO categories (nom, description, ordre_affichage) VALUES
('Cartes Cadeaux Gaming', 'Cartes cadeaux pour plateformes de jeux', 1),
('Abonnements Streaming', 'Codes d\'abonnement Netflix, Spotify, etc.', 2),
('Licences Logicielles', 'Licences pour logiciels et applications', 3),
('Cartes Prépayées', 'Cartes prépayées diverses', 4);

-- Administrateur par défaut (mot de passe: admin123 - À CHANGER!)
INSERT INTO admin (email, password, nom, role) VALUES
('admin@2gk.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur Principal', 'super_admin');

-- Paramètres du site par défaut
INSERT INTO site_settings (cle, valeur, description, type) VALUES
('site_name', '2GK', 'Nom du site', 'text'),
('site_description', 'Votre boutique de produits numériques', 'Description du site', 'text'),
('contact_email', 'contact@2gk.com', 'Email de contact', 'text'),
('kia_api_key', '', 'Clé API KiaPay', 'text'),
('kia_secret_key', '', 'Clé secrète KiaPay', 'text'),
('smtp_host', '', 'Serveur SMTP', 'text'),
('smtp_port', '587', 'Port SMTP', 'number'),
('smtp_username', '', 'Nom d\'utilisateur SMTP', 'text'),
('smtp_password', '', 'Mot de passe SMTP', 'text'),
('recaptcha_site_key', '', 'Clé du site reCAPTCHA', 'text'),
('recaptcha_secret_key', '', 'Clé secrète reCAPTCHA', 'text'),
('maintenance_mode', 'false', 'Mode maintenance', 'boolean');

-- Index pour optimiser les performances
CREATE INDEX idx_products_active ON products(actif);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_codes_product_status ON codes(product_id, statut);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(statut);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_messages_status ON messages(statut);
CREATE INDEX idx_cart_sessions_session ON cart_sessions(session_id);
CREATE INDEX idx_cart_sessions_expiration ON cart_sessions(date_expiration);