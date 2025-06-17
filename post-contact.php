<?php

//création de la var $error pour vérifier la validité des champs du formulaire à ajouter dans le tableau
$errors =[];

//vérification dans le tableau
//si cette clé n;'esite pas (dans le cas du champs nom par exemple ou qu'il est vide)

if(!array_key_exists('name', $_POST) || $_POST['name']==''){
    //erreur au niveau du champ nom
    $errors['name']="Vous n'avez pas renseigné votre nom!";
}

if(!array_key_exists('email', $_POST) || $_POST['email']=='' || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
    //erreur au niveau du champ email
    $errors['email']="Vous n'avez pas renseigné un email valide!";
}


if(!array_key_exists('message', $_POST) || $_POST['message']==''){
    //erreur au niveau du champ message
    $errors['message']="Vous n'avez pas renseigné votre message!";
}

//S'il y a des erreur, retaour à la parge précédente(formulaire contact). sinon, excévuter le traitement.

//création de la session pour enregistrer les différentes erreurs. 
session_start();

if(!empty($errors)){
    
    $_SESSION['errors'] = $errors;//envoie du tableau contenant des erreurs à la var globale $_session
    $_SESSION['inputs'] = $_POST;//envoie des données entrées dans les champs du formulaire
    header('Location: contact.php');
}
else{

        // Déclaration des variables et affectation des chaines vides
    $name = $email  = $message = "";

    /*
    vérifions si le formulaire a été soumis à l'aide de $_SERVER["REQUEST_METHOD"]. 
    Si REQUEST_METHOD est POST, alors le formulaire a été soumis - et il doit être validé. 
    S'il n'a pas été soumis, ignorez la validation et affichez un formulaire vierge.
    */

    function test_input($data) {

        $data = trim($data); //trim — Supprime les espaces (ou d'autres caractères) en début et fin de chaîne
        $data = stripslashes($data);//stripslashes — Supprime les antislashs d'une chaîne
        $data = htmlspecialchars($data);//htmlspecialchars — Convertit les caractères spéciaux en entités HTML
        return $data;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = test_input($_POST["name"]);
    $email = test_input($_POST["email"]);
    $message = test_input($_POST["message"]);
    
    }

    /*Fonction qui permet de lutter contre les hackers et spammeurs lors de la validation des données
    SECRUITE VALIDATION DES DONNEES
    */
    
  

    // En cas de succès càd sans erreur (soumission du formulaire): 

    $_SESSION['success'] =1;
    $headers = 'FROM: ' . $email;

    mail('contact@bowabancongo.com', 'Message de ' . $name, $message, $headers);
    header('Location: contact.php');
}





