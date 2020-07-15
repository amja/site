<?php
	//converts ISO 8601 to UNIX timestamp
	function get_external_timestamp(){

	sscanf($_GET['local_delivery_time'],"%u-%u-%uT%u:%u:%uZ",$year,$month,$day,$hour,$min,$sec);

	$nowstamp=mktime($hour,$min,$sec,$month,$day,$year);
	return $nowstamp;
}


	function get_data($url){

		//uses curl to get json data from website
		$ch = curl_init($url);

		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);

		$dat = curl_exec ($ch);

		curl_close($ch);

		$array = json_decode($dat, true);

		//returns array of data
		return $array;
	}




	function youwin(){

		//returns all is well confirmation
		$return = array('valid' => 'true');

		$die = json_encode($return);

		die($die);
	}

	function Etag(){

		//makes etag
		$today = date('d-m-Y',get_external_timestamp());
		header("ETag: ".md5($today));

	}

	function make_header(){

		//well duh
		include('../header.php');
	}



	function make_contents($username,$pop){

		//connects to mysql db
		connects();

		//Queries db for all entries from entered username
		$query = mysql_query("SELECT * FROM suggest WHERE username = \"".$username."\"")or die(mysql_error());

		//parses the results
		$results = array();

		//puts data into multidimensional array
		while ($line = mysql_fetch_array($query, MYSQL_ASSOC)) {

			$results[] = $line;
		}

		//do this stuff if username is given
		if (!empty($username)) {

			//cleans up and then sql escapes username string
			$a = trim($username);

			$username = mysql_real_escape_string($a);

			//Gets suggestions from friends
			friendjam($results,$username);

		} else {

			//makes popular jam stuff
			popjam();
		}
	}

	function popjam(){

		//Queries to see whether an entry has been made today.
		$query = mysql_query("SELECT * FROM popular WHERE date = \"".date('Y-m-d',get_external_timestamp())."\"")or die(mysql_error());

		//parses results
		$results = mysql_fetch_assoc($query);

		//if an entry has not been made for today, get data and generate html to put it in
		if (!empty($results)) {

			make_header();

			make_html($results,$results['from'],true);

		} else {

			$found = 0;

			$popdata = get_data("http://api.thisismyjam.com/1/popular.json?key=eac1fdc677c64c79aadb70a20d2c7437705474c8");

			//does a sql query for each entry on the popular jams page.
			foreach ($popdata['jams'] as $things) {

				//makes query to see if the chosen jam has already been done
				$query = mysql_query("SELECT * FROM popular WHERE id = \"".$things['id']."\"");

				//parses shit
				$results = mysql_fetch_assoc($query);

				//if the jam hasnt been done, break out of foreach loop and continue.
				if (empty($results)){

					break;
				}

				$found++;
			}

				make_header();
				//generate html with data inside

				make_html($popdata['jams'][$found],$popdata['jams'][$found]['from'],true);

				//adds data to db.
				addpopular(mysql_real_escape_string($popdata['jams'][$found]['id']),mysql_real_escape_string($popdata['jams'][$found]['jamvatarLarge']),
				mysql_real_escape_string($popdata['jams'][$found]['from']),mysql_real_escape_string($popdata['jams'][$found]['title']),
				mysql_real_escape_string($popdata['jams'][$found]['artist']),mysql_real_escape_string($popdata['jams'][$found]['caption']));
			}

	}

	//makes html from inputed array
	function make_html($results,$name,$popular){

		//Use different image for jams if jamvatar isn't present
		if ($results['jamvatarLarge'] != "http://api.thisismyjam.com/includes/image/c/placeholders/sized/player_black_384.png") {

			echo "<img src='http://amos.im/resize.php?i=\"".$results['jamvatarLarge']."\"&w=395' class='dither'>";
		
		} else {

			echo "<img src='http://little.amosjackson.com/nope.png' id='nope' class='dither'>";
		}

		//adds popular star if true is passed as function argument
		if ($popular===true) {

			$starry =  "<img src='http://little.amosjackson.com/popular_star.png' id='star'>";
		}
		echo "
		<p id='datas'>".$starry."<span id='person'>".$results['from']." suggests:</span>".$results['title']." by ".$results['artist']."</p>";

		//changes layout iff there is no caption
		if (!empty($results['caption'])) {

			$captain = "<span id='capt'>&#8220;".$results['caption']."&#8221;</span> ";

		}

			echo "<p id='capturl'>".$captain."TIMJ.am/".$name."</p>";



	}

	function friendjam($results,$username){

			//gets data as array of people the person is following
			$following = get_data("http://api.thisismyjam.com/1/".$username."/following.json?key=eac1fdc677c64c79aadb70a20d2c7437705474c8&order=affinity");

			//prepares variables for counting jams
			$found=0;
			$all = 0;

			//does this for each person iteration in the array
			foreach ($following['people'] as $person) {

				//queries whether their current jam id is in the database
				$jamid = substr($person['currentJam'],-57,7);

				$query = mysql_query("SELECT * FROM `suggest` WHERE `id` = '".$jamid."' AND `username` = '".$username."'")or die(mysql_error());
				//prepares array for parsing
				$someurls = array();
				//more parsing
				while ($line = mysql_fetch_array($query, MYSQL_ASSOC)) {

					$someurls[] = $line;

				}

				//adds more to total jam count
				$all++;

				//only add them if they have a current jam
				if ($person['hasCurrentJam']=="1") {

				//if it doesn't find anything in db, break.
				if (empty($someurls)) {

						break;

					}

				}
					//if found, adds one to the found count
					$found++;

			}

			//if every jam is found, do stuff
			if ($found==$all) {
				//if user wants popular jams after all the suggestions have run out, do stuff.
				if ($_GET['no']!="1") {
					//gets popular jams then dies
					popjam();

					die(make_footer());

				} else {

					//if they dont want any popular, die.
					die();
				}
			}

			//gets data from the selected person
			$chosenurl = "http://api.thisismyjam.com/1/".$following['people'][$found]['name'].".json";
			$chosendata = get_data($chosenurl);
			//$chosendata = get_data($following['people'][$found]['apiUrl']);

			//generates header
			make_header();

			//generates html, yo
			make_html($chosendata['jam'],$chosendata['person']['name'], false);

			//adds data to database
			addsuggest($chosendata['jam']['id'],$username);

	}



	function connects(){

		//connects to mysql
		//$connect = mysql_connect('localhost','little','NfDT4SLDeDTx3YLt')or die("connect to mysql");
		$connect = mysql_connect('localhost','amosjack_user', 'IhtwascTdpaiptl')or die("connect to mysql");
		//selects db
		$db = mysql_select_db('amosjack_little');
	}

	function make_footer(){

		//duh
		include ('footer.php');

	}

	//inserts stuff into 'suggest' table of db
	function addsuggest($id,$u){

		$insert = mysql_query("INSERT INTO suggest VALUES ('".$u."','".$id."','".date('Y-m-d',get_external_timestamp())."', NULL)")or die(mysql_error());

		mysql_close( mysql_connect('localhost','amosjack_user','IhtwascTdpaiptl'));
	}

	//inserts stuff into 'popular' table of db
	function addpopular($id,$j,$f,$t,$a,$c){

		$insert = mysql_query("INSERT INTO popular VALUES ('".$id."','".date('Y-m-d',get_external_timestamp())."','".$j."','".$f."','".$t."','".$a."','".$c."')")or die(mysql_error());

		mysql_close( mysql_connect('localhost','amosjack_user','IhtwascTdpaiptl'));
	}


	?>
