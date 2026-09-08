<?php

    require'adm/config.php';
    //Sécurisation de la validation des données

	function test_input($data){

		$data = trim($data);
		$data = htmlspecialchars($data);
		$data = stripcslashes($data);
		return $data;
    }

    //création de la var $error pour vérifier la validité des champs du formulaire à ajouter dans le tableau
    $errors =[];
    //vérification dans le tableau
    //si cette clé n;'esite pas (dans le cas du champs nom par exemple ou qu'il est vide)

    if(!array_key_exists('name', $_POST) || $_POST['name']==''){
        //erreur au niveau du champ nom
        $errors['name']="Vous n'avez pas renseigné votre nom!";
    }
    if(!array_key_exists('lastname', $_POST) || $_POST['lastname']==''){
        //erreur au niveau du champ prenom
        $errors['lastname']="Vous n'avez pas renseigné votre pénom!";
    }
    if(!array_key_exists('username', $_POST) || $_POST['username']==''){
        //erreur au niveau du champ nom utilisqteur
        $errors['username']="Vous n'avez pas renseigné votre prénom!";
    }
    if(!array_key_exists('email', $_POST) || $_POST['email']=='' || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        //erreur au niveau du champ email
        $errors['email']="Vous n'avez pas renseigné un email valide!";
    }
    if(!array_key_exists('password1', $_POST) || $_POST['password1']==''){
        //erreur au niveau du champ mot de passe1
        $errors['password1']="Vous n'avez pas renseigné le mot de passe!";
    }
    if(!array_key_exists('password2', $_POST) || $_POST['password2']==''){
        //erreur au niveau du champ mot de passe2
        $errors['password2']="Vous n'avez pas renseigné le mot de passe!";
    }
    if($_POST['password1'] != $_POST['password2']){
        //erreur au niveau du champ mot de passe2
        $errors['password2']="Mots de passe différents";
    }

    //création de la session pour enregistrer les différentes erreurs. 
    session_start();

    if(!empty($errors)){
        
        $_SESSION['errors'] = $errors;//envoie du tableau contenant des erreurs à la var globale $_session
        $_SESSION['inputs'] = $_POST;//envoie des données entrées dans les champs du formulaire
        header('Location: account');
    }
    else{

        if ($_SERVER['REQUEST_METHOD'] == "POST"){
                        
            $fistname = test_input($_POST['name']);
            $lastname = test_input($_POST['lastname']);
            $username = test_input($_POST['username']);
            $mail = test_input($_POST['email']);
            $password = test_input($_POST['password2']);

            // Vérifiez que l'adresse e-mail n'est pas déjà utilisée

            $query = "SELECT COUNT(*) AS count FROM user WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$_POST['email']]);

            $count = $stmt->fetchColumn();

            if ($count > 0) {
            echo "L'adresse e-mail est déjà utilisée.";
            exit();
            }

           // Cryptez le mot de passe
            $password = password_hash($_POST['password2'], PASSWORD_BCRYPT);

            // Insérez les données dans la table MySQL
            $sql_query = "INSERT INTO user (nom, lastname, username, email, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_query);
            $stmt->execute([$_POST['name'], $_POST['lastname'], $_POST['username'], $_POST['email'], $password]);

            // Envoyez un e-mail de confirmation
            /*$to = $_POST['email'];
            $subject = "Confirmation d'inscription";
            $message = "Votre compte a été créé avec succès.\n\nVotre identifiant : " . $_POST['Id_User'] . "\n\nVotre mot de passe : " . $_POST['password'];
            mail($to, $subject, $message);
            */
         
            header('Location: account-success');



        }
    }