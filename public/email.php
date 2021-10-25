<?php
// login database info
$user ="azure";
$pass ='6#vWHD_$';
$db ="localdb";

$db =new mysqli('127.0.0.1:49297', $user, $pass, $db) or die ("Unable to connect");

// get the input and store into variable
$email_input=$_POST["email"];
$postcode_input=$_POST["postcode"];
// set the email fomat regular expression
$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 

// set the condition to get the right value
if (strlen($postcode_input) == 4 & preg_match($regex, $email_input)){
    // sql query
    $sql1= "select longitude from `postcodes` where postcode = '$postcode_input'";

    $lo = $db->query($sql1);

    $sql2= "select lat from `postcodes` where postcode = '$postcode_input'";

    $la = $db->query($sql2);

    // if the query get the result
    if ($lo->num_rows > 0 ) {
        // If the variable already exists pop the alert
        $sql_check = "select email from `beekeeper` where email ='$email_input' ";
        $check = $db->query($sql_check);
        if ($check->num_rows > 0 ) {
            echo "<script>alert('You have subscribed.'); location.href = 'index.html#sub'</script>";
        }
        // if it is new value, store into database
        else {
            $sql="INSERT INTO `beekeeper` (email, postcode) VALUES ('$email_input', '$postcode_input')";

            $bk = $db->query($sql);

            echo "<script>alert('Subscribed successfully'); location.href = 'index.html'</script>";


        }

    }
    // pop the alert for wrong formate postcode or email
    else {
        echo "<script>alert('Please enter a postcode of VIC!'); location.href = 'index.html#sub'</script>";
    }
    

}

else {
    echo "<script>alert('Not correct email or postcode!'); location.href = 'index.html#sub'</script>";
}

 ?>

