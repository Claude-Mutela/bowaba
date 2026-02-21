<?php

    session_start();
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require '../phpmailer/src/PHPMailer.php';
    require '../phpmailer/src/SMTP.php';
    require '../phpmailer/src/Exception.php';

    //formatage des données brutes du form 
    function test_input($data) {
        $data = trim($data); //trim — Supprime les espaces (ou d'autres caractères) en début et fin de chaîne
        $data = stripslashes($data);//stripslashes — Supprime les antislashs d'une chaîne
        $data = htmlspecialchars($data);//htmlspecialchars — Convertit les caractères spéciaux en entités HTML
        return $data;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $name = test_input($_POST["name"]);
        $email = test_input($_POST["mail"]);
        $subject = test_input($_POST["subject"]);
        $message = test_input(htmlspecialchars($_POST["message"]));

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'fondation.bowabancongo.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'contact@fondation.bowabancongo.com';
        $mail->Password = 'Motdepasse@fondation2025';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  // Port 465 → SMTPS
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->addReplyTo($email, $name);

        // Destinataire
        $mail->setFrom('contact@fondation.bowabancongo.com', $name);
        $mail->addAddress("contact@fondation.bowabancongo.com", 'Fondation-BOWABA');

        // Contenu du mail
        $mail->isHTML(true);                                  // Format HTML
        $mail->Subject = $subject;
        // Construire le corps du message HTML
        $body = "
        <h5>Nouveau message de $name</h5>
        <p><strong>Email :</strong> $email</p>
        <p>$message</p>";
        $mail->Body = $body;

        try {
            $mail->send();
            $_SESSION['success'] = true; // Message de succès
        } catch (Exception $e) {
            error_log('[fondation/mail] PHPMailer error: ' . $mail->ErrorInfo);
            $_SESSION['error'] = true;
        }
        
        // Redirection vers la page de contact avec le message de succès/erreur
        header("Location: index.php");
        exit();
    }    
?>