<?php 
	
	require'adm_header.php';
	require'../config.php';
	
	  //Récuperation des données envoyées dans l'url

   $recup_title = $_GET['input_search'];


   ///////////////////////////////////

  		//Recherche de l'article

	

		if (isset($_GET['input_search']) AND !empty($_GET['input_search'])) {
    	
    	$search = htmlspecialchars($_GET['input_search']);

    	$sql_search = $conn->prepare('SELECT * FROM `article` WHERE titre_art LIKE "%'.$search.'%"');
    	$sql_search->execute();
    	$sql_reslt = $sql_search->fetchAll();

 	   	}
 	   	else{
 	   		$error_message ='Aucun article ne correspond pas à votre recherche!';
 	   	}
   		

   		foreach($sql_reslt as $recup){

   		}

   		/////////////////////

 
	/*	$sql_select = $sql = "SELECT *FROM article WHERE id_art=$s_id;";
		$sql_select = $conn->prepare($sql_select);
		$sql_select->execute();
		$select_rslt = $sql_select->fetchAll();


		foreach($select_rslt as $d_select){
			  	$d_id = $d_select['id_art'];
		}
	*/
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
 	</style>
 </head>
 <body>

 	<div class="container">
 		<div class="contente">
 			<h2>Résultat de la recherhe</h2>
 		</div>
 		
 		<?php

 			if(empty($recup)){

		 		?>

		<div class="text-center">
 			<div class="btn btn-danger" id="alert">Aucun article ne correspond à votre recherche!</div>
 		</div>

 		
 		<?php 
 			}
 			else{

 				$s_id = $recup['id_art'];
		   		$s_title = $recup['titre_art'];
		   		$s_auteur = $recup['auteur'];
		   		$s_date = $recup['date_art'];


		   		$sql_edit = $sql = "SELECT *FROM article WHERE id_art=$s_id;";
			    $result_sql = $conn->prepare($sql_edit);
			    $result_sql->execute();
			    $rst_sql = $result_sql->fetchAll();


			    foreach($rst_sql as $e_rst){
			    	$e_id = $e_rst['id_art'];
			    	$e_titre = $e_rst['titre_art'];
			    	$e_p1 = $e_rst['p_art1'];
			    	$e_auteur = $e_rst['auteur'];
			    	$e_photo = $e_rst['photo_art'];

			 ?>


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

		  
		    <tr>
		      <th scope="row"><?php echo $s_id; ?></th>
		      <td><?php echo $s_title; ?></td>
		      <td><?php echo $s_auteur; ?></td>
		      <td><?php echo $s_date; ?></td>

		      <td>
		      	<a href="edit_article.php?id_art=<?php echo $e_id; ?>&titre_art=<?php echo $e_titre; ?>&p_art1=<?php echo $e_p1; ?>&p_art2=<?php echo $e_p2; ?>&p_art3=<?php echo $e_p3; ?>&p_art4=<?php echo $e_p4; ?>&p_art5=<?php echo $e_p5; ?>&auteur=<?php echo $e_auteur; ?>&photo=<?php echo $e_photo; ?>" class="btn btn-dark">Editer
		      	</a>
		      		      	
                <form action="delete.php?id_art=<?php echo $recup['id_art']; ?>" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cet article ?')" style="display:inline">
                    <button class="btn btn-danger">Supprimer</button>
                </form>
		      </td>
		    </tr>
 		</table>
			<?php 
		    }
		}
 		?>
 		
 
 </body>
 </html>

