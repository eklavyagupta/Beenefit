<?php

$user ="root";
$pass ='';
$db ="localdb";

$db =new mysqli('127.0.0.1:50190', $user, $pass, $db) or die ("Unable to connect");

$email_input=$_POST["email"];
$postcode_input=$_POST["postcode"];


$sql="INSERT INTO `beekeeper` (email, postcode) VALUES ('$email_input','$postcode_input')";

$bk = $connection->query($sql);

echo "<script>alert('Subscribed successfully'); location.href = 'index.html'</script>";

 ?>

