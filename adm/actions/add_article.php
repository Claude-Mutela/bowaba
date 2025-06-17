<?php 
	
	require'adm_header.php';


    $conn = mysqli_connect("localhost", "root", "", "bowaba");
    $conn->set_charset("utf8");
    if($conn){
      // echo'connected';
   }

   $message ="";

   if(isset($_POST['submit'])){
       $filename = $_FILES['fichier']['name'];
       $filetmpname = $_FILES['fichier']['tmp_name'];
       $folder = 'image/';

       move_uploaded_file($filetmpname, $folder.$filename);

       $title =$_POST['titre'];
       $p1 = $_POST['p1'];
       $auteur = $_POST['auteur'];


       $sq = 'INSERT INTO `article`(`titre_art`, `p_art1`, `auteur`, `photo_art`) VALUES ("'.$title.'","'.$p1.'","'.$auteur.'","'.$filename.'")';
       $qry = mysqli_query($conn, $sq);

       if($qry){
           $message ="L'article a été publié avec succès!";
           // echo "Article Publié";
       }
       else{
        echo "erreur" . $sq . "<br>" . mysqli_error($conn);
       }
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
			display: block;
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
			<h2>Publier un Article</h2>
		</div>
		 <?php 
                if ($message) {?>
                    
                    <div class="alert alert-success">L'article a été publié avec succès!</div>
               <?php }

             ?>
		<form method="post" action="" enctype="multipart/form-data">
			  <div class="mb-3">
			    <label for="exampleInputEmail1" class="form-label">Titre de l'article</label>
			    <input type="text" name="titre" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
			  </div>
			  <div class="mb-3">
			    <label for="exampleInputPassword1" class="form-label">Contenu de l'article </label>
				<textarea name="p1" id="" cols="90" rows="10"></textarea>
			  </div>
			  <div class="mb-3">
			    <label for="exampleInputPassword1" class="form-label">Auteur de l'article</label>
			    <input type="text" name="auteur" class="form-control" id="exampleInputPassword1">
			  </div>
			  <div class="mb-3">
			    <label for="exampleInputPassword1" class="form-label">Photo de l'article</label>
			    <input type="file" name="fichier" class="form-control" id="exampleInputPassword1">
			  </div>
			  
			  <button type="submit" name="submit" class="btn btn-primary">Publier</button>
		</form>
	</div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>
