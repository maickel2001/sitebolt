<?php
require_once 'includes/config.php';

$pageTitle = 'Conditions Générales de Vente';
$pageDescription = 'Consultez les conditions générales de vente de 2GK pour connaître vos droits et obligations lors de vos achats.';

include 'includes/header.php';
?>

<div class="container">
    <div class="legal-page">
        <div class="legal-header">
            <h1>Conditions Générales de Vente</h1>
            <p class="last-updated">Dernière mise à jour : <?php echo date('d/m/Y'); ?></p>
        </div>

        <div class="legal-content">
            <div class="toc">
                <h2>Sommaire</h2>
                <ul>
                    <li><a href="#article1">1. Objet et champ d'application</a></li>
                    <li><a href="#article2">2. Informations sur la société</a></li>
                    <li><a href="#article3">3. Produits et services</a></li>
                    <li><a href="#article4">4. Commandes et paiement</a></li>
                    <li><a href="#article5">5. Livraison</a></li>
                    <li><a href="#article6">6. Garanties</a></li>
                    <li><a href="#article7">7. Remboursements et annulations</a></li>
                    <li><a href="#article8">8. Responsabilité</a></li>
                    <li><a href="#article9">9. Propriété intellectuelle</a></li>
                    <li><a href="#article10">10. Protection des données</a></li>
                    <li><a href="#article11">11. Droit applicable et litiges</a></li>
                </ul>
            </div>

            <article id="article1" class="legal-article">
                <h2>1. Objet et champ d'application</h2>
                <p>Les présentes Conditions Générales de Vente (CGV) régissent les relations contractuelles entre la société exploitant le site 2GK et toute personne physique ou morale souhaitant effectuer un achat via le site.</p>
                <p>Ces CGV s'appliquent à toutes les ventes de produits numériques réalisées sur le site <?php echo SITE_URL; ?>. Elles sont accessibles à tout moment sur le site et prévaudront sur toute autre condition figurant dans tout autre document.</p>
                <p>L'acheteur déclare avoir pris connaissance des présentes CGV et les avoir acceptées en cochant la case prévue à cet effet avant la mise en œuvre de la procédure de commande en ligne.</p>
            </article>

            <article id="article2" class="legal-article">
                <h2>2. Informations sur la société</h2>
                <p>Le site 2GK est exploité par :</p>
                <div class="company-info">
                    <p><strong>Raison sociale :</strong> 2GK</p>
                    <p><strong>Email :</strong> <?php echo SITE_EMAIL; ?></p>
                    <p><strong>Site web :</strong> <?php echo SITE_URL; ?></p>
                </div>
            </article>

            <article id="article3" class="legal-article">
                <h2>3. Produits et services</h2>
                <h3>3.1 Description des produits</h3>
                <p>2GK propose à la vente des produits numériques incluant :</p>
                <ul>
                    <li>Cartes cadeaux pour diverses plateformes (Steam, PlayStation, Xbox, etc.)</li>
                    <li>Codes d'abonnement (Netflix, Spotify, etc.)</li>
                    <li>Licences logicielles</li>
                    <li>Autres produits numériques</li>
                </ul>
                
                <h3>3.2 Disponibilité</h3>
                <p>Les produits proposés sont ceux figurant sur le site au jour de la consultation par l'acheteur et dans la limite des stocks disponibles. En cas d'indisponibilité d'un produit après passation de la commande, l'acheteur en sera informé par email.</p>
                
                <h3>3.3 Prix</h3>
                <p>Les prix sont indiqués en Francs CFA (FCFA) toutes taxes comprises. 2GK se réserve le droit de modifier ses prix à tout moment, étant toutefois entendu que le prix figurant au catalogue le jour de la commande sera le seul applicable à l'acheteur.</p>
            </article>

            <article id="article4" class="legal-article">
                <h2>4. Commandes et paiement</h2>
                <h3>4.1 Processus de commande</h3>
                <p>Pour passer commande, l'acheteur doit :</p>
                <ol>
                    <li>Créer un compte sur le site</li>
                    <li>Ajouter les produits souhaités au panier</li>
                    <li>Valider le contenu du panier</li>
                    <li>Confirmer les informations de facturation</li>
                    <li>Accepter les présentes CGV</li>
                    <li>Procéder au paiement</li>
                </ol>
                
                <h3>4.2 Moyens de paiement</h3>
                <p>Les paiements s'effectuent exclusivement via KiaPay, notre partenaire de paiement sécurisé. Les moyens de paiement acceptés incluent :</p>
                <ul>
                    <li>Cartes bancaires (Visa, Mastercard)</li>
                    <li>Mobile Money</li>
                    <li>Autres moyens acceptés par KiaPay</li>
                </ul>
                
                <h3>4.3 Confirmation de commande</h3>
                <p>Toute commande figurant sur le site suppose l'adhésion aux présentes CGV. Toute confirmation de commande entraîne acceptation des prix et descriptions des produits disponibles à la vente.</p>
            </article>

            <article id="article5" class="legal-article">
                <h2>5. Livraison</h2>
                <h3>5.1 Modalités de livraison</h3>
                <p>Les produits numériques sont livrés par voie électronique :</p>
                <ul>
                    <li><strong>Livraison automatique :</strong> Les codes sont envoyés immédiatement après confirmation du paiement</li>
                    <li><strong>Livraison manuelle :</strong> Les codes sont traités et envoyés sous 24h maximum</li>
                </ul>
                
                <h3>5.2 Réception</h3>
                <p>Les codes sont envoyés à l'adresse email associée au compte client et sont également disponibles dans l'espace client sur le site.</p>
                
                <h3>5.3 Problèmes de livraison</h3>
                <p>En cas de non-réception dans les délais indiqués, l'acheteur doit contacter le service client via le formulaire de contact du site.</p>
            </article>

            <article id="article6" class="legal-article">
                <h2>6. Garanties</h2>
                <h3>6.1 Garantie de fonctionnement</h3>
                <p>2GK garantit que tous les codes livrés sont authentiques et fonctionnels au moment de la livraison. En cas de dysfonctionnement d'un code, l'acheteur doit en informer le service client dans les 24h suivant la réception.</p>
                
                <h3>6.2 Limitation de garantie</h3>
                <p>La garantie ne s'applique pas en cas :</p>
                <ul>
                    <li>D'utilisation incorrecte du code</li>
                    <li>De tentative d'utilisation sur une région non compatible</li>
                    <li>D'utilisation après expiration (le cas échéant)</li>
                </ul>
            </article>

            <article id="article7" class="legal-article">
                <h2>7. Remboursements et annulations</h2>
                <h3>7.1 Droit de rétractation</h3>
                <p>Conformément à la réglementation en vigueur sur les produits numériques, le droit de rétractation ne s'applique pas aux codes numériques livrés et utilisables immédiatement.</p>
                
                <h3>7.2 Remboursement exceptionnel</h3>
                <p>Un remboursement peut être accordé dans les cas suivants :</p>
                <ul>
                    <li>Code défaillant non remplaçable</li>
                    <li>Erreur de notre part dans la livraison</li>
                    <li>Impossibilité technique de livraison</li>
                </ul>
                
                <h3>7.3 Modalités de remboursement</h3>
                <p>Les remboursements sont effectués par le même moyen de paiement que celui utilisé pour l'achat, dans un délai de 14 jours ouvrés.</p>
            </article>

            <article id="article8" class="legal-article">
                <h2>8. Responsabilité</h2>
                <p>La responsabilité de 2GK ne pourra être engagée en cas :</p>
                <ul>
                    <li>D'utilisation non conforme des codes livrés</li>
                    <li>De suspension ou fermeture de comptes par les plateformes tierces</li>
                    <li>De modifications des conditions d'utilisation des plateformes tierces</li>
                    <li>De force majeure ou cas fortuit</li>
                </ul>
                <p>En tout état de cause, la responsabilité de 2GK est limitée au montant de la commande concernée.</p>
            </article>

            <article id="article9" class="legal-article">
                <h2>9. Propriété intellectuelle</h2>
                <p>Tous les éléments du site 2GK sont et restent la propriété intellectuelle et exclusive de 2GK. Personne n'est autorisé à reproduire, exploiter, rediffuser, ou utiliser à quelque titre que ce soit, même partiellement, des éléments du site qu'ils soient logiciels, visuels ou sonores.</p>
                <p>Les marques et logos des produits vendus appartiennent à leurs propriétaires respectifs.</p>
            </article>

            <article id="article10" class="legal-article">
                <h2>10. Protection des données personnelles</h2>
                <p>Les informations personnelles collectées lors de la commande font l'objet d'un traitement informatique destiné à :</p>
                <ul>
                    <li>La gestion des commandes</li>
                    <li>La livraison des produits</li>
                    <li>Le service client</li>
                    <li>La prévention de la fraude</li>
                </ul>
                <p>Conformément à la loi, l'acheteur dispose d'un droit d'accès, de rectification et de suppression des données le concernant. Pour exercer ce droit, il suffit de nous contacter via <?php echo SITE_EMAIL; ?>.</p>
                <p>Pour plus d'informations, consultez notre <a href="<?php echo SITE_URL; ?>/politique-confidentialite">Politique de confidentialité</a>.</p>
            </article>

            <article id="article11" class="legal-article">
                <h2>11. Droit applicable et litiges</h2>
                <h3>11.1 Droit applicable</h3>
                <p>Les présentes CGV sont soumises au droit en vigueur. Toute contestation ou litige portant sur l'application ou l'interprétation du présent contrat sera de la compétence des tribunaux compétents.</p>
                
                <h3>11.2 Médiation</h3>
                <p>En cas de litige, l'acheteur peut recourir à une procédure de médiation conventionnelle ou à tout autre mode alternatif de règlement des différends.</p>
                
                <h3>11.3 Contact</h3>
                <p>Pour toute question relative aux présentes CGV, vous pouvez nous contacter :</p>
                <ul>
                    <li>Par email : <?php echo SITE_EMAIL; ?></li>
                    <li>Via le formulaire de contact : <a href="<?php echo SITE_URL; ?>/contact">Nous contacter</a></li>
                </ul>
            </article>

            <div class="legal-footer">
                <p><strong>Date d'entrée en vigueur :</strong> <?php echo date('d/m/Y'); ?></p>
                <p>2GK se réserve le droit de modifier les présentes CGV à tout moment. Les modifications entrent en vigueur dès leur publication sur le site.</p>
            </div>
        </div>
    </div>
</div>

<style>
.legal-page {
    max-width: 900px;
    margin: 0 auto;
    background: var(--card-bg);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.legal-header {
    background: linear-gradient(135deg, var(--highlight-color), #ff6b9d);
    color: white;
    padding: 3rem 2rem;
    text-align: center;
}

.legal-header h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.last-updated {
    font-size: 1rem;
    opacity: 0.9;
}

.legal-content {
    padding: 2rem;
}

.toc {
    background: var(--input-bg);
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 3rem;
}

.toc h2 {
    color: var(--highlight-color);
    margin-bottom: 1.5rem;
}

.toc ul {
    list-style: none;
    padding: 0;
}

.toc li {
    margin-bottom: 0.8rem;
}

.toc a {
    color: var(--text-color);
    text-decoration: none;
    padding: 0.5rem;
    display: block;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.toc a:hover {
    background: var(--accent-color);
    color: var(--highlight-color);
    padding-left: 1rem;
}

.legal-article {
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.legal-article:last-child {
    border-bottom: none;
}

.legal-article h2 {
    color: var(--highlight-color);
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--highlight-color);
}

.legal-article h3 {
    color: var(--text-color);
    font-size: 1.3rem;
    margin: 2rem 0 1rem 0;
}

.legal-article p {
    color: var(--text-color);
    line-height: 1.8;
    margin-bottom: 1rem;
    text-align: justify;
}

.legal-article ul,
.legal-article ol {
    margin: 1rem 0 1rem 2rem;
    color: var(--text-color);
}

.legal-article li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

.company-info {
    background: var(--input-bg);
    padding: 1.5rem;
    border-radius: 8px;
    margin: 1rem 0;
}

.company-info p {
    margin-bottom: 0.5rem;
}

.legal-footer {
    background: var(--input-bg);
    padding: 2rem;
    border-radius: 10px;
    margin-top: 3rem;
    text-align: center;
}

.legal-footer p {
    margin-bottom: 1rem;
    color: var(--text-muted);
}

.legal-content a {
    color: var(--highlight-color);
    text-decoration: none;
}

.legal-content a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .legal-header {
        padding: 2rem 1rem;
    }
    
    .legal-header h1 {
        font-size: 2rem;
    }
    
    .legal-content {
        padding: 1rem;
    }
    
    .toc {
        padding: 1.5rem;
    }
    
    .legal-article h2 {
        font-size: 1.5rem;
    }
    
    .legal-article h3 {
        font-size: 1.2rem;
    }
    
    .legal-article ul,
    .legal-article ol {
        margin-left: 1.5rem;
    }
}

/* Smooth scrolling pour les ancres */
html {
    scroll-behavior: smooth;
}

/* Style pour les ancres ciblées */
.legal-article:target {
    background: rgba(233, 69, 96, 0.05);
    border-radius: 10px;
    padding: 1rem;
    margin: -1rem;
    animation: highlight 2s ease-out;
}

@keyframes highlight {
    0% {
        background: rgba(233, 69, 96, 0.2);
    }
    100% {
        background: rgba(233, 69, 96, 0.05);
    }
}
</style>

<?php include 'includes/footer.php'; ?>