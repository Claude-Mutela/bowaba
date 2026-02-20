<?php 
    $user = 'bowabanc_kipay';
    $pass = 'Motdepasse@2023/';

    // $user = 'root';
    // $pass = 'root';

    try{
        $conn = new PDO("mysql:host=localhost;dbname=bowabanc_db;charset=utf8", $user, $pass);

        //echo'Connected';
    }
    catch(PDOException $e){
        echo 'connexion failed: ' . $e->getMessage();
    }
?>
