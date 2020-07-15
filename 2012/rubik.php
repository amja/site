<?php
$moves = array("F","F'","B","B'","L","L'","R","R'","U","U'","D","D'","F2","B2","L2","R2","U2","D2");
?>
<html>
<head>
<title>Scramble Generator</title>
<link href='http://fonts.googleapis.com/css?family=Lustria' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
<style>
body
{

	background:url('http://subtlepatterns.com/patterns/pinstriped_suit.png');
	margin:0px;
	padding:0px;
	text-align:center;
	text-decoration:none!important;

}
#ribbon
{
	position:absolute;
	background:rgba(0,0,0,0.4);
	width:100%;
	height:75px;
	top:50%;
	margin-top:-38px;
	font-family:Lustria;
	color:white;
	font-size:38px;
	text-align:center;
	padding-top:21px;
	word-spacing:3px;

}
header
{
margin:0 auto;
margin-left:-324px;
left:50%;
top:25%;
position:absolute;
}
h1
{
	font-family:Droid Sans;
	color:white;
	font-size:75px;
	margin:0px;
	padding:0px;
	text-decoration:none;
}
h1:hover
{

	opacity:0.7;
}
a{
	color:white;
	text-decoration:none;
}
h2{
		
		color:gray;
		font-family:Lustria;
		font-size:25px;
}

#exp
{
	width:500px;
	position:relative;
	margin:0 auto;
	top:75px;
	background:rgba(0,0,0,0.4);
	border-radius:15px;
	font-size:30px;
	color:white;
}
</style>
<link rel="icon" type="image/png" href="resources/rbk.png">
</head>
<body>

	<header>
	<a href="javascript:location.reload(true)"><h1>Scramble Generator</h1></a>
	<h2>For Rubik's Cube</h2>
	</header>
	<div id="ribbon">

		<?php 
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)]." ";
			echo $moves[rand(0,17)];
		?>
	<div>
<div id="exp">
This project generates random combinations of moves for scrambling a 3x3 Rubik's Cube. If you are not familiar with these notations, click <a href="http://en.wikipedia.org/wiki/Rubik's_Cube#Move_notation" target="_blank">{here}</a>
</div>

</body>
</html>