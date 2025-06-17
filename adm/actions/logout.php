<?php
session_start();

// Fonction pour déconnecter l'utilisateur et détruire la session
function logout() {
    // Vider toutes les variables de session
    $_SESSION = array();

    // Détruire la session
    session_destroy();

    // Rediriger vers la page de connexion
    header("Location: ../../login.php");
    exit();
}
logout();
?>