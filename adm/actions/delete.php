<?php 

	require'../config.php';


	$recup_id = $_GET['id_art'];

	$sql_delete = $conn->prepare("DELETE FROM article WHERE id_art='$recup_id'");
	$sql_delete->execute();

	

 ?>
 <!DOCTYPE html>
 <html>
 <head>
 	<meta charset="utf-8">
 	<meta name="viewport" content="width=device-width, initial-scale=1">
 	<title></title>
 </head>
 <body>
 
 		<?php if ($sql_delete){
 		?>

 		<script> 
 			alert("Article supprimé avec succès!");
 		</script>

 		<?php 	
 		header("location: dashbord.php");
 		} 
 		else{
 		?>
 		
 		<script> 
 			alert("Impossible de supprimer l'article!");
 		</script>
 		<?php 	
 		}
 		?>

 </body>
 </html>
