<?php

$user ="azure";
$pass ='6#vWHD_$';
$db ="localdb";

$db =new mysqli('127.0.0.1:50190', $user, $pass, $db) or die ("Unable to connect");

$email_input=$_POST["email"];
$postcode_input=$_POST["postcode"];
$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 


if (strlen($postcode_input) == 4 & preg_match($regex, $email_input)){

    $sql1= "select longitude from `postcodes` where postcode = '$postcode_input'";

    $lo = $db->query($sql1);

    $sql2= "select lat from `postcodes` where postcode = '$postcode_input'";

    $la = $db->query($sql2);

    if ($lo->num_rows > 0 ) {
        $sql_check = "select email,postcode from `beekeeper` where postcode = '$postcode_input' and email ='$email_input' ";
        $check = $db->query($sql_check);
        if ($check->num_rows > 0 ) {
            echo "<script>alert('You have subscribed to this postcode, try a new one!'); location.href = 'index.html#sub'</script>";
        }
        else {
            $sql="INSERT INTO `beekeeper` (email, postcode) VALUES ('$email_input', '$postcode_input')";

            $bk = $db->query($sql);

            echo "<script>alert('Subscribed successfully'); location.href = 'index.html'</script>";


        }

    }
    else {
        echo "<script>alert('Not correct postcode!'); location.href = 'index.html#sub'</script>";
    }
    

}

else {
    echo "<script>alert('Not correct email or postcode!'); location.href = 'index.html#sub'</script>";
}

 ?>

