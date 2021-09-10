<?php

$user ="azure";
$pass ='6#vWHD_$';
$db ="localdb";

$db =new mysqli('127.0.0.1:50190', $user, $pass, $db) or die ("Unable to connect");

$email_input=$_POST["email"];
$postcode_input=$_POST["postcode"];

if (strlen($postcode_input) == 4 & preg_match("/^[a-zA-Z-' ]*$/",$email_input)){

    $sql1= "select longitude from `postcodes` where postcode = '$postcode_input'";

    $lo = $db->query($sql1);

    $sql2= "select lat from `postcodes` where postcode = '$postcode_input'";

    $la = $db->query($sql2);

    if ($lo->num_rows > 0 ) {
    // output data of each row
    $sql="INSERT INTO `beekeeper` (email, postcode) VALUES ('$email_input', '$postcode_input')";

    $bk = $db->query($sql);

    echo "<script>alert('Subscribed successfully'); location.href = 'index.html#sub'</script>";

    }
    else {
        echo "<script>alert('Not correct postcode!'); location.href = 'index.html#sub'</script>";
    }
    

}

else {
    echo "<script>alert('Not correct email or postcode!'); location.href = 'index.html#sub'</script>";
}




 ?>

