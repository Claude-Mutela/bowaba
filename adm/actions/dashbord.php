<?php 
	
	require'adm_header.php';
	require'../config.php';
	

	// Affichage des articles

	$sql_edit = $sql = "SELECT *FROM article;";
    $result_sql = $conn->prepare($sql_edit);
    $result_sql->execute();
    $rst_sql = $result_sql->fetchAll();


    foreach($rst_sql as $e_rst){
    	$e_id = $e_rst['id_art'];
    	$e_titre = $e_rst['titre_art'];
    	$e_p1 = $e_rst['p_art1'];
    	$e_auteur = $e_rst['auteur'];
    	$e_photo = $e_rst['photo_art'];
    }

    ///////////////////////////////////////////////////////////////

 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<meta charset="utf-8">
 	<meta name="viewport" content="width=device-width, initial-scale=1">
 	<title></title>

 	<style>
 		.container{
 			margin-top: 50px;
 		}
 		.container .contente{
 			margin-bottom: 30px;
 		}
 		.container .contente h2{
 			text-align: center;
 		}
 		.navbar-expand-lg{
 			margin-bottom: 50px;
 		}
 		.navbar-expand-lg input{
 			margin-right: 20px;
			width: 500px;
			margin-left: 50px;
 		}
 		.navbar-expand-lg button{
 			color: #0d6efd;
 			border-color: #0d6efd;
 		}
 		.navbar-expand-lg button:hover{
 			color: #fff;
 			background-color: #0d6efd;
 		}
		.container table tr #title-article{
			width: 500px;
		}
 	</style>
 </head>
 <body>

 	<div class="container">
 		<div class="contente">
 			<h2>Tableau de bord</h2>
 		</div>
 		<nav class="navbar navbar-expand-lg navbar-light">
			      	<form class="d-flex" method="GET" action="search.php">
				    	<input class="form-control me-8" name="input_search" type="search" placeholder="Rechercher un article" required>
				      	<input type="submit" name="search" value="Rechercher" class="btn btn-primary">
				        
			     	</form>
		</nav>
		
 			<table class="table table-striped">
 			
		  <thead>
		    <tr>
		      <th scope="col">#</th>
		      <th scope="col">Titres Articles</th>
		      <th scope="col">Auteurs</th>
		      <th scope="col">Date de Publication</th>
		      <th scope="col">Actions</th>
		    </tr>
		  </thead>
		  <tbody>

		  	<?php foreach ($rst_sql as $tb):?> 

		    <tr>
		      <th scope="row"><?php echo$tb['id_art']; ?></th>
		      <td id="title-article"><?php echo$tb['titre_art']; ?></td>
		      <td><?php echo$tb['auteur']; ?></td>
		      <td><?php echo$tb['date_art']; ?></td>

		      <td>
		      	<a href="edit_article.php?id_art=<?php echo $e_id; ?>&titre_art=<?php echo $e_titre; ?>&p_art1=<?php echo $e_p1; ?>&auteur=<?php echo $e_auteur; ?>&photo=<?php echo $e_photo; ?>" class="btn btn-dark">Editer
		      	</a>
                <form action="delete.php?id_art=<?php echo $tb['id_art']; ?>" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cet article ?')" style="display:inline">
                    <button class="btn btn-danger">Supprimer</button>
                </form>
		      </td>
		    </tr>
		    <?php endforeach ?>
		  </tbody>
		</table>
 	</div>
 
 </body>
 </html>
 <?php
// Dans la page login.php

if (isset($_POST['logout'])) {
    // Détruire la session
    session_destroy();

    // Rediriger l'utilisateur à la page de connexion
    header("Location: ../../login.php");
    exit();
}


?>
