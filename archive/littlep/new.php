<?php

include 'functions.php';
$array = get_stuff("http://api.thisismyjam.com/1/Han/following.json");

$jamming = array();
//Gets all users who have current jams
foreach($array['people'] as $data){
	if($data['hasCurrentJam'] === true){
		//Takes their jams url
		array_push($jamming, $data['jams']);
	}
}

foreach($jamming as $id){
		

}

?>