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

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<title></title>
</head>
<body>

<!-- Navbar -->

		<nav class="navbar navbar-expand-lg navbar-light bg-primary">
			<div class="container-fluid">
			    <a class="navbar-brand" href="dashbord.php">Tableau de bord</a>
			    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
			    <span class="navbar-toggler-icon"></span>
			    </button>
			    <div class="collapse navbar-collapse" id="navbarNav">
			      <ul class="navbar-nav">
			        <li class="nav-item">
			          <a class="nav-link" href="add_article.php">Publier un article</a>
			        </li>
			        <li class="nav-item">
			          <a class="nav-link" href="add_service.php">Ajouter un service</a>
			        </li>
					<?php
					// Vérifier si l'utilisateur est déjà connecté
						if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
							// Ajoutez le lien de déconnexion dans le header
							?>
					<li class="nav-item">
			          <a class="nav-link" href="logout.php">Se Déconnecter</a>
			        </li>
					<?php
						}
					?>
					
			      </ul>
			    </div>
			  </div>
		</nav>

<!-- main -->
	
	







</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>