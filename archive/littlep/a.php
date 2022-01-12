<?php
$username = "amosjack_user";
$password = "IhtwascTdpaiptl";

$conn = new PDO('mysql:host=localhost;dbname=amosjack_little', $username, $password);
 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $data = $conn->query('SELECT * FROM suggest WHERE id = "3sjtieu"');
    foreach($data as $row){

    	echo "yes";
    }


?>