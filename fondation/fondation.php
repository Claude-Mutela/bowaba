<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fondation Bowabancongo - Professionnalisation des acteurs non étatiques</title>

    <meta name="description" content="Fondation Bowaba - Professionnalisation des acteurs non étatiques en RDC. Formations en entrepreneuriat, agrobusiness, gestion et renforcement des capacités pour OSC et MPME.">
      <!-- Favicons -->
    <link href="assets/img/logo/icone-fondation-bowaba.png" rel="icon">
    <link href="assets/img/logo" rel="apple-touch-icon">

    <meta name="description" content="Fondation Bowaba - Professionnalisation des acteurs non étatiques en RDC. Formations en entrepreneuriat, agrobusiness, gestion et renforcement des capacités pour OSC et MPME.">
    <meta name="application-name" content="Fondation Bawaba n congo-Website">
    <meta name="author" content="Bowaba n Congo">
    <link rel="author" href="https://fondation.bowabancongo.com/">
    <meta name="application-name" content="Bowaba n Congo-Website">
    <meta name="keywords" content="fondation bowaba, formation entrepreneuriat RDC, renforcement capacités Congo, agrobusiness Kinshasa, coaching MPME Afrique, 
    OSC République Démocratique Congo, formation gestion projet, financement organisations congolaises, entrepreneuriat féminin RDC, développement durable Congo">
    <meta name="creator" content="Bowaba n Congo">
    <meta name="publisher" content="Bowaba n Congo">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/realisation.css">
</head>

<body>
    <!-- Header & Navigation -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">
                    <img src="assets/img/logo/logo.png" alt="logo fondation bowaba n congo">
                    <span>Fondation BOWABA</span>
                </a>
                <ul class="nav-links">
                    <li><a href="#home">Accueil</a></li>
                    <li><a href="#about">À propos</a></li>
                    <li><a href="#activities">Activités</a></li>
                    <li><a href="#missions">Missions</a></li>
                    <li><a href="#objectives">Objectifs</a></li>
                    <li><a href="#achievements">Réalisations </a></li>
                    <li><a href="#training">Formation</a></li>
                    <li><a href="#agrobusiness">Agrobusiness</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
                <div class="hamburger">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Carousel -->
    <section class="hero" id="home">
        <div class="carousel">
            <!-- Slide 1 -->
            <div class="slide active" style="background-image: url('assets/img/slides/slide3.jpg');">
                <div class="slide-content">
                    <h1>Renforcement des Capacités Professionnelles</h1>
                    <p>Nous professionnalisons les acteurs non étatiques pour augmenter leurs capacités dans la mobilisation des fonds et l'absorption des capitaux.</p>
                    <a href="#about" class="btn">En savoir plus</a>
                    <a href="#contact" class="btn btn-accent">Nous contacter</a>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="slide" style="background-image: url('assets/img/slides/slide2.jpeg');">
                <div class="slide-content">
                    <h1>Formation et Accompagnement Entrepreneurial</h1>
                    <p>Nous accompagnons les MPME et OSC portées par des femmes et des jeunes pour concrétiser leurs projets innovants.</p>
                    <a href="#training" class="btn">Nos formations</a>
                    <!-- <a href="#contact" class="btn btn-accent">S'inscrire</a> -->
                </div>
            </div>

            <!-- Slide 3 -->
             <div class="slide" style="background-image: url('assets/img/slides/slide1.jpg');">
                <div class="slide-content">
                    <h1>Entrepreneuriat Agricole et Innovation</h1>
                    <p>Développement des compétences en agrobusiness pour une agriculture durable et rentable.</p>
                    <a href="#agrobusiness" class="btn">Découvrir</a>
                    <!-- <a href="#contact" class="btn btn-accent">Participer</a> -->
                </div>
            </div>

            <div class="carousel-controls">
                <div class="control-dot active" data-slide="0"></div>
                <div class="control-dot" data-slide="1"></div>
                <div class="control-dot" data-slide="2"></div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <h2>À propos de nous</h2>
            <div class="about-content">
                <div class="about-image">
                    <img src="assets/img/about.jpg" alt="Fondacteur FOB-Wise Kipey">
                </div>
                <div class="about-text">
                    <h3>Fondation Bowabancongo (FOB ASBL)</h3>
                    <p>La Fondation Bowabancongo est une organisation à but non lucratif représentée par Monsieur KIPEY EZEKIEL E. Wise, en tant que Président de la fondation.</p>
                    <p><strong>Objectif principal :</strong> Professionnaliser les acteurs non étatiques afin d'augmenter leurs capacités dans la mobilisation des fonds et l'absorption des capitaux.</p>
                    <p><strong>Domaines d'intervention :</strong></p>
                    <ul>
                        <li>Actions Humanitaires (assistance aux personnes vulnérables)</li>
                        <li>Droits humains (promotion et protection des droits humains) et justice transitionnelle</li>
                        <li>Entrepreneuriat (promotion de la culture entrepreneuriale)</li>
                        <li>Entrepreneuriat Agricole</li>
                        <li>Éducation, culture et arts</li>
                        <li>Genre, famille et enfant</li>
                        <li>Santé et protection de l'environnement</li>
                    </ul>
                    <a href="#contact" class="btn">Nous contacter</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Activities Section -->
    <section class="activities" id="activities">
        <div class="container">
            <h2>Nos Activités</h2>
            <div class="cards-container">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3>Accompagnement</h3>
                    <p>Accompagnement des MPME et OSC portées par des femmes et des jeunes pour concrétiser leurs projets innovants.</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Formation</h3>
                    <p>Formations en renforcement des capacités techniques et entrepreneuriales pour divers secteurs d'activité.</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Coaching</h3>
                    <p>Coaching personnalisé pour aider les bénéficiaires à développer leurs compétences et leurs entreprises.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Missions Section -->
    <section class="missions" id="missions">
        <div class="container">
            <h2>Nos Missions</h2>
            <div class="cards-container">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Renforcement des capacités</h3>
                    <p>Renforcer les capacités administratives et de gestion des organisations et entreprises.</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3>Outils de gestion</h3>
                    <p>Concevoir des outils de gestion et d'administration adaptés aux besoins des bénéficiaires.</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Mise en consortium</h3>
                    <p>Regrouper et mettre en consortium des organisations autour des thématiques communes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Objectives Section -->
    <section class="objectives" id="objectives">
        <div class="container">
            <h2>Nos Objectifs</h2>
            <div class="cards-container">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h3>Communication</h3>
                    <p>Définir les mécanismes de communication et de valorisation des œuvres d'esprit et réalisations des organisations.</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3>Accompagnement</h3>
                    <p>Identifier et accompagner les organisations à forte potentialité œuvrant dans l'informel pour leur formalisation.</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <h3>Autonomisation</h3>
                    <p>Autonomiser et diversifier les ressources financières des organisations par la création d'activités génératrices de revenus.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- AgroBusiness Section -->
    <section class="agrobusiness" id="agrobusiness">
        <div class="container">
            <h2>Agrobusiness</h2>
            <p class="text-center">Nos programmes en entrepreneuriat agricole visent à développer des compétences clés pour réussir dans le secteur agricole.</p>

            <div class="cards-container">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h3>Entrepreneuriat agricole</h3>
                    <p>Formation complète sur les fondamentaux de l'entrepreneuriat dans le secteur agricole.</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h3>Chaine de valeur</h3>
                    <p>Comprendre et maîtriser la chaîne de valeur dans le secteur agricole pour maximiser les profits.</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Étude de marché</h3>
                    <p>Apprendre à réaliser des études de marché pertinentes pour les produits agricoles.</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Planning de production</h3>
                    <p>Méthode RAM pour une planification efficace de la production agricole.</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-business-time"></i>
                    </div>
                    <h3>Business Model</h3>
                    <p>Développer un modèle économique viable pour son entreprise agricole.</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3>Marketing</h3>
                    <p>Techniques de marketing de terrain et digital pour promouvoir ses produits agricoles.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Réalisations Section -->
    <section class="achievements" id="achievements">
        <div class="container">
            <h2>Nos Réalisations</h2>
            <p class="text-center">Découvrez quelques-unes de nos réalisations marquantes à travers le pays.</p>

            <div class="achievements-grid">
                <!-- Card 1 -->
                <div class="achievement-card">
                    <div class="card-image">
                        <img src="assets/img/realisation/rel6.jpg">
                    </div>
                    <div class="card-overlay">
                        <h3>Formation en renforcement des capacités</h3>
                        <p>Formation et coaching personnalisé des personnes affectées par le projet d'aménagement hydroagricole dans la commune de Nsele, Kinshasa.</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="achievement-card">
                    <div class="card-image">
                        <img src="assets/img/realisation/rel5.jpg">
                    </div>
                    <div class="card-overlay">
                        <h3>Accompagnement entrepreneurial</h3>
                        <p>Projet d'accompagnement de 119 femmes entrepreneurs productrices de chitwangues pour le développement durable de leurs micro-entreprises.</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="achievement-card">
                    <div class="card-image">
                        <img src="assets/img/realisation/rel1.jpg">
                    </div>
                    <div class="card-overlay">
                        <h3>Formation agricole</h3>
                        <p>Formation et accompagnement entrepreneurial des producteurs agricoles dans le cadre du Programme APEFE/Haut-Katanga à Lubumbashi.</p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="achievement-card">
                    <div class="card-image">
                        <img src="assets/img/realisation/rel3.jpg">
                    </div>
                    <div class="card-overlay">
                        <h3>Restructuration organisationnelle</h3>
                        <p>Diagnostic et rapport pour la restructuration organisationnelle de l'ONG locale AASD, avec production d'un manuel de procédures.</p>
                    </div>
                </div>

                <!-- Card 5 -->
                <div class="achievement-card">
                    <div class="card-image">
                        <img src="assets/img/realisation/rel2.jpg">
                    </div>
                    <div class="card-overlay">
                        <h3>Coaching entrepreneurial</h3>
                        <p>Programme de coaching entrepreneurial pour autonomiser les jeunes apprenants par la création de micro-entreprises.</p>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="achievement-card">
                    <div class="card-image">
                        <img src="assets/img/realisation/rel4.jpg">
                    </div>
                    <div class="card-overlay">
                        <h3>Formation en entrepreneuriat</h3>
                        <p>Formation de 1 000 jeunes entrepreneurs en entrepreneuriat dans le cadre du projet Talents Pluriels lancé par CUSO INTERNATIONAL.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Training Section -->
    <section class="training" id="training">
        <div class="container">
            <h2>Nos Formations</h2>
            <p class="text-center">Découvrez notre catalogue complet de formations professionnelles adaptées à vos besoins.</p>

            <div class="training-categories">
                <!-- Gestion Category -->
                <div class="category">
                    <h3>Gestion Entrepreneuriale</h3>
                    <div class="training-list">
                        <div class="training-item">
                            <h4>Entrepreneuriat <span class="training-duration">1 semaine</span></h4>
                            <p>Formation aux fondamentaux de l'entrepreneuriat.</p>
                        </div>
                        <div class="training-item">
                            <h4>Business Analysis (Analyse des affaires) <span class="training-duration">2 semaines</span></h4>
                            <p>Techniques d'analyse des opportunités commerciales.</p>
                        </div>
                        <div class="training-item">
                            <h4>Elaboration du Business Model et Business Plan <span class="training-duration">2 semaines</span></h4>
                            <p>Méthodologie pour créer des modèles économiques et plans d'affaires solides.</p>
                        </div>
                        <div class="training-item">
                            <h4>Gestion des projets avec MS-PROJECT <span class="training-duration">2 semaines</span></h4>
                            <p>Maîtrise du logiciel pour une gestion efficace des projets.</p>
                        </div>
                        <div class="training-item">
                            <h4>Planification, Suivi et Evaluation des projets <span class="training-duration">2 semaines</span></h4>
                            <p>Outils pour planifier, suivre et évaluer les projets d'entreprise.</p>
                        </div>
                        <div class="training-item">
                            <h4>Gestion de production <span class="training-duration">3 semaines</span></h4>
                            <p>Maîtrise de la chaîne de production et gestion de stock MP et PF d'une MPMI.</p>
                        </div>
                    </div>
                </div>

                <!-- Informatique Category -->
                <div class="category">
                    <h3>Informatique</h3>
                    <div class="training-list">
                        <div class="training-item">
                            <h4>Pack Office (Word, Excel, Power Point) <span class="training-duration">1 mois</span></h4>
                            <p>Maîtrise des outils bureautiques essentiels.</p>
                        </div>
                        <div class="training-item">
                            <h4>Logiciels d'analyse statistiques <span class="training-duration">3 semaines</span></h4>
                            <p>Utilisation de Spss, Stata, Excel avancé et Power BI.</p>
                        </div>
                        <div class="training-item">
                            <h4>Conception et Création des sites web <span class="training-duration">1 mois</span></h4>
                            <p>Avec WordPress, Elementor et Woocommerce.</p>
                        </div>
                        <div class="training-item">
                            <h4>Design Graphic <span class="training-duration">2 mois</span></h4>
                            <p>Formation complète en conception graphique.</p>
                        </div>
                    </div>
                </div>

                <!-- Santé Category -->
                <div class="category">
                    <h3>Santé</h3>
                    <div class="training-list">
                        <div class="training-item">
                            <h4>Epidémiologie de terrain <span class="training-duration">2 semaines</span></h4>
                            <p>Techniques d'épidémiologie appliquée.</p>
                        </div>
                        <div class="training-item">
                            <h4>Technique d'enquête en santé publique <span class="training-duration">1 semaine</span></h4>
                            <p>Méthodologie pour mener des enquêtes en santé.</p>
                        </div>
                        <div class="training-item">
                            <h4>Wash (eau hygiène et assainissement) <span class="training-duration">2 semaines</span></h4>
                            <p>Principes fondamentaux de l'eau, l'hygiène et l'assainissement.</p>
                        </div>
                    </div>
                </div>

                <!-- Agrobusiness Category -->
                <div class="category">
                    <h3>Agrobusiness</h3>
                    <div class="training-list">
                        <div class="training-item">
                            <h4>Entrepreneuriat agricole <span class="training-duration">2 séances</span></h4>
                            <p>Fondamentaux de l'entrepreneuriat dans le secteur agricole.</p>
                        </div>
                        <div class="training-item">
                            <h4>Chaine de valeur <span class="training-duration">2 séances</span></h4>
                            <p>Comprendre et optimiser la chaîne de valeur agricole.</p>
                        </div>
                        <div class="training-item">
                            <h4>Création d'une entreprise agricole <span class="training-duration">3 semaines</span></h4>
                            <p>Formalisation et aspects juridiques des entreprises agricoles.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
        <div class="container">
            <h2>Contactez-nous</h2>
            <div class="contact-container">
                <div class="contact-info">
                    <h3>Informations de contact</h3>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4>Adresse</h4>
                            <p>
                                01, Avenue LUAMBO-MAKIADI, Gallerie ATTOUÉ, <br>
                                local 307, Kin-Mazière, Commune de la Gombe.
                            </p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <h4>Téléphone</h4>
                            <p>
                                <a href="tel:+243816695000" target="_blank">+243 816 695 000</a>
                            </p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4>Email</h4>
                            <p>
                                <a href="mailto:contact@fondation.bowabancongo.com">contact@fondation.bowabancongo.com</a>
                            </p>
                        </div>
                    </div>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="contact-form">
                    <h3>Envoyez-nous un message</h3>
                    <form action="mail.php" method="post">
                        <div class="form-group">
                            <?php if(isset($_SESSION['success'])) : ?>
                                <p class="success-message btn-status">Message envoyé!</p>
                            <?php endif ?>

                            <?php if(isset($_SESSION['error'])) : ?>
                                <p class="error-message btn-status"> Message non envoye, Ressayé !</p>
                            <?php endif ?>
                        </div>
                        <div class="form-group">
                            <label for="name">Nom complet</label>
                            <input type="text" name="name" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="mail" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Sujet</label>
                            <input type="text" name="subject" id="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea name="message" id="message" required></textarea>
                        </div>
                        <button type="submit" class="btn">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Fondation Bowabancongo</h3>
                    <p>Professionnaliser les acteurs non étatiques afin d'augmenter leurs capacités dans la mobilisation des fonds et l'absorption des capitaux.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Liens rapides</h3>
                    <ul class="footer-links">
                        <li><a href="#home">Accueil</a></li>
                        <li><a href="#about">À propos</a></li>
                        <li><a href="#activities">Activités</a></li>
                        <li><a href="#achievements">Réalisations </a></li>
                        <li><a href="#training">Formations</a></li>
                        <li><a href="#agrobusiness">Agrobusiness</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Nos formations</h3>
                    <ul class="footer-links">
                        <li><a href="#training">Gestion Entrepreneuriale</a></li>
                        <li><a href="#training">Informatique</a></li>
                        <li><a href="#training">Santé</a></li>
                        <li><a href="#training">Agrobusiness</a></li>
                        <li><a href="#training">Langues</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt"></i>
                            01, Avenue LUAMBO-MAKIADI, Gallerie ATTOUÉ, <br>
                            local 307, Kin-Mazière, Commune de la Gombe.
                        </li>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <a href="tel:+243816695000" target="_blank">+243 816 695 000</a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:contact.fondation@bowabancongo.com">contact@fondation.bowabancongo.com</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y') ?> Fondation Bowabancongo. Tous droits réservés.</p>
                <p class="signature">Designed & Developed by <a href="https://claude-mutela.dev" target="_blank"> Claude Mutela</a> </p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script src="assets/js/realisation.js"></script>
    
    <script type="application/ld+json">
        {
            "@context": "https://fondation.bowabancongo.com/",
            "@type": "NGO",
            "name": "Fondation Bowaba",
            "description": "Organisation spécialisée dans le renforcement des capacités des acteurs non étatiques en RDC",
            "url": "https://fondation.bowabancongo.com/",
            "logo": "https://fondation.bowabancongo.com/assets/img/logo/icone-fondation-bowaba.png",
            "address": 
                {
                    "@type": "PostalAddress",
                    "addressLocality": "Kinshasa",
                    "addressCountry": "CD"
                },
            "contactPoint": 
            {
                "@type": "ContactPoint",
                "telephone": "+243 816 695 000",
                "contactType": "Customer service",
                "email": "contact@fondation.bowabancongo.com"
            }
        }
</script>
</body>

</html>