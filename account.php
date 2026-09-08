<?php
    session_start();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte utilisateur</title>
    <link rel="stylesheet" href="login/css/account-style.css">
    <link rel="icon" href="assets/img/icone-bw.png" >
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>

    <div class="container">
    <!-- Gestion des erreurs stockées dans la  $_session -->
        <?php if(array_key_exists('errors', $_SESSION)): ?>

            <div class="alert alert-danger">
                <?= implode('<br>', $_SESSION['errors']);?>
            </div>
        
        <?php unset($_SESSION['errors']); endif; ?>
        
        <?php if(array_key_exists('success', $_SESSION)): ?>
                <div class="alert alert-success">
                   Incription reussit effectué !
                </div>
                
            <?php 
                unset($_SESSION['success']); endif; 
                
            ?>


        <form class="form" method="POST" action="post-account">
                <p class="title">Inscription </p>
                <p class="message"> Créez le compte pour vous connecter</p>
                <div class="flex">
                <label>
                    <input name="name" placeholder="" type="text" class="input" required>
                    <span>Nom</span>
                </label>

                <label>
                    <input name="lastname" placeholder="" type="text" class="input" required>
                    <span>Prénom</span>
                </label>
            </div>  
                <label>
                    <input name="username" placeholder="" type="text" class="input" required>
                    <span>Nom d'utilisateur</span>
                </label>

                <label>
                    <input name="email" placeholder="" type="email" class="input" required>
                    <span>E-mail</span>
                </label> 
                    
                <label>
                    <input name="password1" placeholder="" type="password" class="input" required>
                    <span>Mot de passe</span>
                </label>
                <label>
                    <input name="password2" placeholder="" type="password" class="input">
                    <span>Confirmer le Mot de passe</span>
                </label>
                <button class="submit">S'inscrire</button>
                <p class="signin">Vous avez déjà un compte ? <a href="login.php">Connectez-vous</a> </p>
            </form>
    </div>
        
</body>
</html>