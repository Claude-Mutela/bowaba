<?php
    session_start();
    $pageTitle='Contactez-nous - Bowaba n Congo';
    $pageDesc='Une question ? Un projet ? Contactez Bowaba n Congo par téléphone, email ou via notre formulaire.';
    $nav='contact';
    require'hd-ft/hd.php';
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Bowaba</title>

    <meta content="Contact, Bowaba, Email, Téléphone, Adresse, Kinshasa" name="keywords">

 <!-- Favicons -->
 <link href="assets/img/icone-bw.png" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!--  CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!--  CSS File -->
  <link href="assets/css/style-contact.css" rel="stylesheet">
</head>
<body>

    <!-- ======= Hero Section (New Static) ======= -->
    <section id="contact-hero" class="d-flex align-items-center" style="background-color: #0b2341; padding: 100px 0 60px; clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);">
        <div class="container text-center text-white">
            <h1 style="font-family: 'Raleway', sans-serif; font-weight: 800; font-size: 3rem;">Contactez-nous</h1>
            <p style="font-size: 1.2rem; opacity: 0.9;">Une question ? Un projet ? Parlons-en.</p>
        </div>
    </section>

    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact section-bg" style="padding-top: 40px;">
        <div class="container" data-aos="fade-up">

            <div class="row gy-4">
                
                <!-- Colonne Gauche : Coordonnées -->
                <div class="col-lg-5 d-flex align-items-stretch">
                    <div class="info-wrapper w-100 p-4 bg-white rounded-3 shadow-sm" style="border-top: 5px solid var(--col);">
                        <div class="section-title text-start pb-3">
                            <h2>Nos Coordonnées</h2>
                            <p>Retrouvez-nous ou contactez-nous directement via les moyens ci-dessous.</p>
                        </div>

                        <div class="info-item d-flex align-items-center mb-4">
                            <div class="icon-box d-flex align-items-center justify-content-center rounded-circle bg-light text-primary flex-shrink-0" style="width: 60px; height: 60px; font-size: 24px;">
                                <i class="bx bx-map"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-1 fw-bold text-dark">Adresse</h4>
                                <p class="mb-0 text-muted">01, Avenue LUAMBO-MAKIADI, Gallerie ATTOUÉ, local 307, Kin-Mazière, Gombe.</p>
                            </div>
                        </div>

                        <div class="info-item d-flex align-items-center mb-4">
                             <div class="icon-box d-flex align-items-center justify-content-center rounded-circle bg-light text-primary flex-shrink-0" style="width: 60px; height: 60px; font-size: 24px;">
                                <i class="bx bx-envelope"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-1 fw-bold text-dark">Email</h4>
                                <p class="mb-0"><a href="mailto:contact@bowabancongo.com" class="text-muted">contact@bowabancongo.com</a></p>
                            </div>
                        </div>

                        <div class="info-item d-flex align-items-center mb-4">
                             <div class="icon-box d-flex align-items-center justify-content-center rounded-circle bg-light text-primary flex-shrink-0" style="width: 60px; height: 60px; font-size: 24px;">
                                <i class="bx bx-phone-call"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-1 fw-bold text-dark">Téléphone</h4>
                                <p class="mb-0"><a href="tel:+243816695000" class="text-muted">+243 816 695 000</a></p>
                            </div>
                        </div>

                        <!-- Map Embed (Optionnel, garder de la place) -->
                        <!-- <div class="mt-4 rounded overflow-hidden" style="height: 200px; background: #eee;"></div> -->
                    </div>
                </div>

                <!-- Colonne Droite : Formulaire -->
                <div class="col-lg-7">
                    <div class="form-wrapper w-100 p-4 bg-white rounded-3 shadow-lg h-100">
                        <div class="section-title text-start pb-3">
                             <h2>Envoyez un message</h2>
                             <p>Remplissez le formulaire ci-dessous et nous vous répondrons dans les plus brefs délais.</p>
                        </div>
                        
                         <!-- Gestion des erreurs/succès -->
                        <?php if(isset($_SESSION['errors'])): ?>
                            <div class="alert alert-danger"><?= implode('<br>', $_SESSION['errors']); ?></div>
                            <?php unset($_SESSION['errors']); ?>
                        <?php endif; ?>
                        
                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">Votre message a bien été envoyé !</div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>

                        <form action="post-contact" method="post" role="form" class="php-email-form-custom">
                            <!-- Honeypot field (hidden from users, visible to bots) -->
                            <div style="display:none;">
                                <label for="website">Leave this field blank</label>
                                <input type="text" name="website" id="website" value="">
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="name" class="form-label fw-bold text-muted small text-uppercase">Nom complet</label>
                                    <input type="text" name="name" class="form-control form-control-lg bg-light border-0" id="name" placeholder="Votre Nom" required value="<?= isset($_SESSION['inputs']['name']) ? $_SESSION['inputs']['name'] : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                     <label for="email" class="form-label fw-bold text-muted small text-uppercase">Email</label>
                                    <input type="email" class="form-control form-control-lg bg-light border-0" name="email" id="email" placeholder="Votre Email" required value="<?= isset($_SESSION['inputs']['email']) ? $_SESSION['inputs']['email'] : ''; ?>">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="subject" class="form-label fw-bold text-muted small text-uppercase">Sujet</label>
                                <input type="text" class="form-control form-control-lg bg-light border-0" name="subject" id="subject" placeholder="Sujet de votre message" required value="<?= isset($_SESSION['inputs']['subject']) ? $_SESSION['inputs']['subject'] : ''; ?>">
                            </div>
                            <div class="form-group mb-4">
                                 <label for="message" class="form-label fw-bold text-muted small text-uppercase">Message</label>
                                <textarea class="form-control form-control-lg bg-light border-0" name="message" rows="6" placeholder="Comment pouvons-nous vous aider ?" required><?= isset($_SESSION['inputs']['message']) ? $_SESSION['inputs']['message'] : ''; ?></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm" style="background: var(--bg); border: none;">Envoyer le message <i class="bx bx-paper-plane ms-2"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

  <!-- JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  

  <!--  JS File -->
  <script src="assets/js/main.js"></script>
  <script src="assets/js/c_main.js"></script></body>
<?php
    require'hd-ft/ft.php';
?>
</html>
<?php 
//Netoyage des sessions
unset($_SESSION['inputs']);
unset($_SESSION['success']);
unset($_SESSION['errors']);

?>  