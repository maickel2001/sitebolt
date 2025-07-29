<?php
require_once 'includes/config.php';

$pageTitle = 'Politique de confidentialité';
$pageDescription = 'Découvrez comment 2GK protège et utilise vos données personnelles conformément à la réglementation en vigueur.';

include 'includes/header.php';
?>

<div class="container">
    <div class="legal-page">
        <div class="legal-header">
            <h1>Politique de confidentialité</h1>
            <p class="last-updated">Dernière mise à jour : <?php echo date('d/m/Y'); ?></p>
        </div>

        <div class="legal-content">
            <div class="intro-section">
                <p>Chez 2GK, nous nous engageons à protéger votre vie privée et vos données personnelles. Cette politique de confidentialité explique comment nous collectons, utilisons, stockons et protégeons vos informations personnelles.</p>
            </div>

            <article class="legal-article">
                <h2>1. Responsable du traitement</h2>
                <p>Le responsable du traitement des données personnelles est :</p>
                <div class="company-info">
                    <p><strong>2GK</strong></p>
                    <p>Email : <?php echo SITE_EMAIL; ?></p>
                    <p>Site web : <?php echo SITE_URL; ?></p>
                </div>
            </article>

            <article class="legal-article">
                <h2>2. Données collectées</h2>
                <h3>2.1 Données d'identification</h3>
                <ul>
                    <li>Nom et prénom</li>
                    <li>Adresse email</li>
                    <li>Numéro de téléphone (optionnel)</li>
                    <li>Adresse postale (optionnel)</li>
                </ul>

                <h3>2.2 Données de connexion</h3>
                <ul>
                    <li>Adresse IP</li>
                    <li>Type de navigateur</li>
                    <li>Pages visitées</li>
                    <li>Date et heure de connexion</li>
                </ul>

                <h3>2.3 Données de commande</h3>
                <ul>
                    <li>Historique des achats</li>
                    <li>Montants des transactions</li>
                    <li>Méthodes de paiement utilisées</li>
                    <li>Codes livrés</li>
                </ul>
            </article>

            <article class="legal-article">
                <h2>3. Finalités du traitement</h2>
                <p>Nous utilisons vos données personnelles pour :</p>
                <ul>
                    <li><strong>Gestion des comptes :</strong> Création et gestion de votre compte utilisateur</li>
                    <li><strong>Traitement des commandes :</strong> Validation, paiement et livraison des produits</li>
                    <li><strong>Service client :</strong> Réponse à vos questions et résolution des problèmes</li>
                    <li><strong>Communication :</strong> Envoi d'emails transactionnels et informatifs</li>
                    <li><strong>Sécurité :</strong> Prévention de la fraude et protection du site</li>
                    <li><strong>Amélioration :</strong> Analyse des performances et amélioration de nos services</li>
                </ul>
            </article>

            <article class="legal-article">
                <h2>4. Base légale du traitement</h2>
                <p>Le traitement de vos données personnelles est fondé sur :</p>
                <ul>
                    <li><strong>Exécution du contrat :</strong> Pour le traitement des commandes et la livraison</li>
                    <li><strong>Intérêt légitime :</strong> Pour la sécurité, la prévention de la fraude et l'amélioration des services</li>
                    <li><strong>Consentement :</strong> Pour les communications marketing (optionnel)</li>
                    <li><strong>Obligation légale :</strong> Pour la conservation des données comptables</li>
                </ul>
            </article>

            <article class="legal-article">
                <h2>5. Partage des données</h2>
                <p>Nous ne vendons jamais vos données personnelles. Nous pouvons les partager uniquement avec :</p>
                <ul>
                    <li><strong>Prestataires de paiement :</strong> KiaPay pour le traitement des paiements</li>
                    <li><strong>Prestataires techniques :</strong> Hébergement et maintenance du site</li>
                    <li><strong>Autorités compétentes :</strong> En cas d'obligation légale</li>
                </ul>
                <p>Tous nos partenaires sont tenus de respecter la confidentialité de vos données.</p>
            </article>

            <article class="legal-article">
                <h2>6. Conservation des données</h2>
                <p>Nous conservons vos données personnelles pendant :</p>
                <ul>
                    <li><strong>Données de compte :</strong> Jusqu'à la suppression du compte + 1 an</li>
                    <li><strong>Données de commande :</strong> 10 ans (obligation comptable)</li>
                    <li><strong>Données de connexion :</strong> 1 an maximum</li>
                    <li><strong>Messages de contact :</strong> 3 ans maximum</li>
                </ul>
            </article>

            <article class="legal-article">
                <h2>7. Vos droits</h2>
                <p>Conformément à la réglementation, vous disposez des droits suivants :</p>
                
                <h3>7.1 Droit d'accès</h3>
                <p>Vous pouvez demander l'accès aux données personnelles que nous détenons sur vous.</p>
                
                <h3>7.2 Droit de rectification</h3>
                <p>Vous pouvez demander la correction de données inexactes ou incomplètes.</p>
                
                <h3>7.3 Droit à l'effacement</h3>
                <p>Vous pouvez demander la suppression de vos données dans certains cas.</p>
                
                <h3>7.4 Droit à la limitation</h3>
                <p>Vous pouvez demander la limitation du traitement de vos données.</p>
                
                <h3>7.5 Droit à la portabilité</h3>
                <p>Vous pouvez demander la transmission de vos données dans un format structuré.</p>
                
                <h3>7.6 Droit d'opposition</h3>
                <p>Vous pouvez vous opposer au traitement de vos données pour des raisons légitimes.</p>
                
                <h3>7.7 Exercice des droits</h3>
                <p>Pour exercer ces droits, contactez-nous à : <?php echo SITE_EMAIL; ?></p>
                <p>Nous répondrons dans un délai d'un mois maximum.</p>
            </article>

            <article class="legal-article">
                <h2>8. Sécurité des données</h2>
                <p>Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour protéger vos données :</p>
                <ul>
                    <li>Chiffrement des données sensibles</li>
                    <li>Accès restreint aux données personnelles</li>
                    <li>Surveillance et journalisation des accès</li>
                    <li>Sauvegardes régulières et sécurisées</li>
                    <li>Formation du personnel à la protection des données</li>
                </ul>
            </article>

            <article class="legal-article">
                <h2>9. Cookies et technologies similaires</h2>
                <p>Notre site utilise des cookies pour :</p>
                <ul>
                    <li><strong>Cookies essentiels :</strong> Fonctionnement du site et du panier</li>
                    <li><strong>Cookies de session :</strong> Maintien de la connexion</li>
                    <li><strong>Cookies de sécurité :</strong> Protection contre les attaques</li>
                </ul>
                <p>Vous pouvez configurer votre navigateur pour refuser les cookies, mais cela peut affecter le fonctionnement du site.</p>
            </article>

            <article class="legal-article">
                <h2>10. Transferts internationaux</h2>
                <p>Vos données personnelles sont traitées et stockées localement. En cas de transfert vers des pays tiers, nous nous assurons que des garanties appropriées sont en place pour protéger vos données.</p>
            </article>

            <article class="legal-article">
                <h2>11. Modifications de la politique</h2>
                <p>Nous pouvons modifier cette politique de confidentialité à tout moment. Les modifications importantes vous seront notifiées par email ou par un avis sur le site.</p>
                <p>La version en vigueur est toujours disponible sur cette page avec la date de dernière mise à jour.</p>
            </article>

            <article class="legal-article">
                <h2>12. Contact et réclamations</h2>
                <p>Pour toute question concernant cette politique ou le traitement de vos données :</p>
                <ul>
                    <li>Email : <?php echo SITE_EMAIL; ?></li>
                    <li>Formulaire de contact : <a href="<?php echo SITE_URL; ?>/contact">Nous contacter</a></li>
                </ul>
                <p>Si vous estimez que vos droits ne sont pas respectés, vous pouvez déposer une réclamation auprès de l'autorité de protection des données compétente.</p>
            </article>

            <div class="legal-footer">
                <p><strong>Date d'entrée en vigueur :</strong> <?php echo date('d/m/Y'); ?></p>
                <p>Cette politique de confidentialité fait partie intégrante de nos <a href="<?php echo SITE_URL; ?>/cgv">Conditions Générales de Vente</a>.</p>
            </div>
        </div>
    </div>
</div>

<style>
.intro-section {
    background: var(--input-bg);
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 3rem;
    border-left: 4px solid var(--highlight-color);
}

.intro-section p {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--text-color);
    margin: 0;
}
</style>

<?php include 'includes/footer.php'; ?>