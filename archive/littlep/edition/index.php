<?php
	
	//Includes php file that does everything
	require ('../functions.php');
	
	//Makes Etag (required)
	Etag();
	header("Content-Type: text/html; charset=utf-8");
	
	//Makes all the stuff (including header)
	make_contents($_GET['u'],$_GET['p']);
	
	//Makes footer (a bit obvious)
	make_footer();
	
	

?>

