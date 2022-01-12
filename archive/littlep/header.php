<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' href='http://little.amosjackson.com/style.css'>
</head>
<body><header>
<img src="http://little.amosjackson.com/header.png" id="wordmark">
<span id="date"><?php sscanf($_GET['local_delivery_time'],"%u-%u-%uT%u:%u:%uZ",$year,$month,$day,$hour,$min,$sec);
	$nowstamp=mktime($hour,$min,$sec,$month,$day,$year);echo date('l j F Y',$nowstamp)?></span>
</header>