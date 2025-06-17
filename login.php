<?php 

	$nav = 'login';
	require'adm/config.php';

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
		<?php if ($nav){
			echo'AUTHENTIFICATION';
		}
		else{
			echo'BOWABA N CONGO';
		}
		 ?>
	</title>

	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="login/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="login/vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/css/util.css">
	<link rel="stylesheet" type="text/css" href="login/css/style.css">
<!--===============================================================================================-->
</head>

<body style="background-color: #666666;">
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form method="POST" action="" class="login100-form validate-form">
					<span class="login100-form-title p-b-43">
						Se Connecter
					</span>
					
					<?php 
						
						// Définition des variables de session
						session_start();
						$user_id = null;

						// Vérification de l'existence d'une session
						if (isset($_SESSION['user_id'])) 
						{
							// L'utilisateur est déjà connecté, on le redirige vers le dashboard
							//header("Location: adm/actions/dashbord.php");
							//exit;
						}

						// Vérification de la soumission du formulaire de connexion
						if ($_SERVER['REQUEST_METHOD'] == 'POST') {
							// Récupération des données du formulaire
							$username = $_POST['username'];
							$password = $_POST['password'];

							// Préparation de la requête SQL
							$stmt = $conn->prepare("SELECT id_user, username, password FROM user WHERE username = :username");
							$stmt->bindParam(":username", $username);

							// Exécution de la requête SQL
							$stmt->execute();

							// Récupération du résultat de la requête SQL
							$user = $stmt->fetch(PDO::FETCH_ASSOC);

							// Vérification des informations de connexion
							if ($user && password_verify($password, $user['password'])) {
								// Les informations de connexion sont correctes, on crée une session
								$_SESSION['loggedin'] = true;

								// Redirection vers le dashboard
								header("Location: adm/actions/dashbord.php");
								exit;
							} else {
								// Les informations de connexion sont incorrectes, on affiche un message d'erreur
								$error_message = "Nom d'utilisateur ou mot de passe incorrects";
							}
						}
					?>
					<?php if (isset($error_message)) { ?>
						<p style="color: red;">
							<?php echo $error_message; ?>
						</p>
					<?php } ?>
					
					<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100" type="text" name="username">
						<span class="focus-input100"></span>
						<span class="label-input100">Nom d'utilisateur</span>
					</div>
					
					
					<div class="wrap-input100 validate-input" data-validate="Password is required">
						<input class="input100" type="password" name="password">
						<span class="focus-input100"></span>
						<span class="label-input100">Mot de passe</span>
					</div>

					<div class="flex-sb-m w-full p-t-3 p-b-32">
						<div class="contact100-form-checkbox">
							<input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
							<label class="label-checkbox100" for="ckb1">
								Se souvenir de moi
							</label>
						</div>

						<div>
							<a href="#" class="txt1">
								Mot de passe oublié?
							</a>
						</div>
					</div>
			

					<div class="container-login100-form-btn">
						<button type="submit" name="login" class="login100-form-btn">
							Se connecter
						</button>
						<p>Vous n'avez pas de compte ?  <a href="account.php">Créer un compte</a></p><br>
						<div>
							<a href="index.php">Quitter</a>
						</div>
					</div>
					
				</form>

				<div class="login100-more" style="background-image: url('assets/img/img.png');">
				</div>
			</div>
		</div>
	</div>
	


	
	
<!--===============================================================================================-->
	<script src="login/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="login/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="login/vendor/bootstrap/js/popper.js"></script>
	<script src="login/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="login/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="login/vendor/daterangepicker/moment.min.js"></script>
	<script src="login/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="login/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="login/js/main.js"></script>

</body>
</html>