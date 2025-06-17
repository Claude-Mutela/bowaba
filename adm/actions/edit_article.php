<?php 
	
	require'adm_header.php';
	require'../config.php';


	$konn = mysqli_connect("localhost", "root", "", "bowaba");
    $konn->set_charset("utf8");
    if($konn){
      // echo'connected';
   }

   //Récuperation des données envoyées dans l'url

   if(!empty($_GET['id_art'])){
   $recup_id = $_GET['id_art'];
   $recup_title = $_GET['titre_art'];
   $recup_p1 = $_GET['p_art1'];
   $recup_auteur = $_GET['auteur'];
   $recup_photo = $_GET['photo'];

   $message ="";

   if(isset($_POST['submit'])){
       $filename = $_FILES['fichier']['name'];
       $filetmpname = $_FILES['fichier']['tmp_name'];
       $folder = 'image/';

       move_uploaded_file($filetmpname, $folder.$filename);

       $title =$_POST['titre'];
       $p1 = $_POST['p1'];
       $auteur = $_POST['auteur'];


     $sql = "UPDATE `article` SET `titre_art` = '".$title."',`p_art1` = '".$p1."',`auteur` = '".$auteur."' WHERE `article`.`id_art` = '$recup_id'";
       $qry = mysqli_query($konn, $sql);

       if($qry){
           $message ="L'article a été mis à jour avec succès!";
          // echo "Article Publié";
       }
       else{
        echo "erreur" . $sq . "<br>" . mysqli_error($konn);
   		    }
	   }   
	}
	else{
?>
	
	<script>
		alert("Aucun article n'a été séléctionné");
	</script>

<?php
	header("location: dashbord.php");
}


 ?>
 <!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


	<style>
		.container{
			margin: 80px auto;
		}
		.container label{
			font-size: 18px;
			font-weight: 400;
		}
		.container .top-title{
			margin: 20px;
			text-align: center;
		}
		.container .top-title h2{
			font-weight: 700;
		}
	</style>
</head>
<body>

<!-- main -->

	<div class="container">
		<div class="top-title">
			<h2>Edition de l'article</h2>
		</div>
		 	<?php 
                if ($message) {?>
                    
                    <script>
						window.alert("Article modifié avec succès!");
					</script>			
               <?php
				header('Location: dashbord.php');   
			}

             ?>


		<form method="post" action="" enctype="multipart/form-data">
			  <div class="mb-3">
			    <label for="exampleInputEmail1" class="form-label">Titre de l'article</label>
			    <textarea rows="8" type="text" name="titre" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"><?php echo $recup_title; ?>
			    </textarea>
			  </div>
			  <div class="mb-3">
			    <label for="exampleInputPassword1" class="form-label">Contenu de l'article</label>
			    <textarea rows="8" type="text" name="p1" class="form-control" id="exampleInputPassword1"><?php echo $recup_p1; ?>
			    </textarea>
			  </div>
			  <div class="mb-3">
			    <label for="exampleInputPassword1" class="form-label">Auteur de l'article</label>
			    <input type="text" name="auteur" class="form-control" id="exampleInputPassword1" value="<?php echo $recup_auteur; ?>">
			  </div>
			 <div class="mb-3">
			    <label for="exampleInputPassword1" class="form-label">Photo de l'article</label>
			    <input type="file" name="fichier" class="form-control" id="exampleInputPassword1" value="<?php echo $recup_photo; ?>">
			  </div>
			  
			  <button type="submit" name="submit" class="btn btn-primary">Editer</button>
		</form>
	</div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>
