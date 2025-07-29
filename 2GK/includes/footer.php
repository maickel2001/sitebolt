    </main>
    
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>2GK</h3>
                    <p>Votre boutique de confiance pour tous vos produits numériques. Cartes cadeaux, codes d'abonnement, licences logicielles et bien plus encore.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Liens rapides</h3>
                    <a href="<?php echo SITE_URL; ?>">Accueil</a>
                    <a href="<?php echo SITE_URL; ?>/catalogue">Catalogue</a>
                    <a href="<?php echo SITE_URL; ?>/categories">Catégories</a>
                    <a href="<?php echo SITE_URL; ?>/contact">Contact</a>
                </div>
                
                <div class="footer-section">
                    <h3>Catégories</h3>
                    <a href="<?php echo SITE_URL; ?>/category/cartes-cadeaux-gaming">Cartes Cadeaux Gaming</a>
                    <a href="<?php echo SITE_URL; ?>/category/abonnements-streaming">Abonnements Streaming</a>
                    <a href="<?php echo SITE_URL; ?>/category/licences-logicielles">Licences Logicielles</a>
                    <a href="<?php echo SITE_URL; ?>/category/cartes-prepayees">Cartes Prépayées</a>
                </div>
                
                <div class="footer-section">
                    <h3>Informations légales</h3>
                    <a href="<?php echo SITE_URL; ?>/cgv">CGV</a>
                    <a href="<?php echo SITE_URL; ?>/mentions-legales">Mentions légales</a>
                    <a href="<?php echo SITE_URL; ?>/politique-confidentialite">Politique de confidentialité</a>
                    <a href="<?php echo SITE_URL; ?>/faq">FAQ</a>
                </div>
                
                <div class="footer-section">
                    <h3>Nous contacter</h3>
                    <p><i class="fas fa-envelope"></i> <?php echo SITE_EMAIL; ?></p>
                    <p><i class="fas fa-clock"></i> Lundi - Vendredi : 9h - 18h</p>
                    <div class="social-links" style="margin-top: 1rem;">
                        <a href="#" style="margin-right: 1rem; color: var(--highlight-color);"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="margin-right: 1rem; color: var(--highlight-color);"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="margin-right: 1rem; color: var(--highlight-color);"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tous droits réservés. | Livraison instantanée de produits numériques</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <?php if (isset($additionalJS)): ?>
        <?php echo $additionalJS; ?>
    <?php endif; ?>
    
    <!-- Script pour maintenir l'état de connexion côté client -->
    <?php if ($isLoggedIn): ?>
    <script>
        sessionStorage.setItem('user_logged_in', 'true');
    </script>
    <?php else: ?>
    <script>
        sessionStorage.removeItem('user_logged_in');
    </script>
    <?php endif; ?>
</body>
</html>