<?php 
	
	require'../config.php';



	function search(){

		if (isset($_GET['input_search']) AND !empty($_GET['input_search'])) {
    	
    	$search = htmlspecialchars($_GET['input_search']);

    	$sql_search = $conn->prepare('SELECT * FROM `article` WHERE titre_art LIKE "%'.$search.'%"');
    	$sql_search->execute();
    	
    }
    $sql_reslt = $sql_search->fetchAll();
}


