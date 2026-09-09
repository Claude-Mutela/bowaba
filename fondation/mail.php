<?php

    session_start();

    use PHPMailer\PHPMailer\PHPMailer;

    // Chargement de la factory SMTP centralisée (lit les secrets depuis .env)
    require_once __DIR__ . '/../kon/mailer.php';

    //formatage des données brutes du form 
    function test_input($data) {
        $data = trim($data); //trim — Supprime les espaces (ou d'autres caractères) en début et fin de chaîne
        $data = stripslashes($data);//stripslashes — Supprime les antislashs d'une chaîne
        $data = htmlspecialchars($data);//htmlspecialchars — Convertit les caractères spéciaux en entités HTML
        return $data;
    }

    if (($_SERVER["REQUEST_METHOD"] ?? '') === "POST") {

        $name    = test_input($_POST["name"] ?? '');
        $email   = test_input($_POST["mail"] ?? '');
        $subject = test_input($_POST["subject"] ?? '');
        $message = test_input($_POST["message"] ?? '');

        $mail = null;
        try {
            // Création de l'instance PHPMailer via la factory (credentials lus depuis .env)
            $mail = createMailer('fondation');

            $mail->addReplyTo($email, $name);

            // Destinataire
            $destinataire = env('FONDATION_SMTP_FROM', 'contact@fondation.bowabancongo.com');
            $mail->addAddress($destinataire, 'Fondation-BOWABA');

            // Contenu du mail
            $mail->isHTML(true);                                  // Format HTML
            $mail->Subject = $subject;
            // Construire le corps du message HTML
            $nameHtml    = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            $emailHtml   = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
            $messageHtml = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

            $body = "
            <h5>Nouveau message de {$nameHtml}</h5>
            <p><strong>Email :</strong> {$emailHtml}</p>
            <p>{$messageHtml}</p>";
            $mail->Body = $body;

            $mail->send();
            $_SESSION['success'] = true; // Message de succès
        } catch (\Throwable $e) {
            error_log('[fondation/mail] Erreur envoi email : ' . $e->getMessage());
            if ($mail instanceof PHPMailer && !empty($mail->ErrorInfo)) {
                error_log('[fondation/mail] PHPMailer ErrorInfo : ' . $mail->ErrorInfo);
            }
            $_SESSION['error'] = true;
        }
        
        // Redirection vers la page de contact avec le message de succès/erreur
        header("Location: index.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
?>