<?php

$user ="azure";
$pass ='6#vWHD_$';
$db ="localdb";

$db =new mysqli('127.0.0.1:50190', $user, $pass, $db) or die ("Unable to connect");

$weather_check= mysqli_query($db,"select lat, longitude from `beekeeper`b,`postcodes`p where p.postcode = b.postcode");

while($row = mysqli_fetch_array($weather_check)) {
    $lat_list[] = $row['lat'];
    $longitude_list[] = $row['longitude'];
 }
 /*print_r($lat_list);
 print_r($longitude_list);*/
foreach( $lat_list as $index => $lat ) {

    
    $ApiUrl ='https://api.openweathermap.org/data/2.5/onecall?lat='.$lat_list[$index].'&lon='.$longitude_list[$index].'&exclude=current,minutely,hourly,alerts&appid=f8602df67c495efda45f5097b5244ba6&units=metric';
    
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $ApiUrl);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    curl_close($ch);
    $data = json_decode($response);


    
    $Time = $data ->daily[1] ->dt;
    $dateInLocal = date("Y-m-d", $Time);
    $min_temp = $data ->daily[1] ->temp->min;
    $max_temp = $data ->daily[1] ->temp->max;

    $get_postcode = "  select distinct postcode from `beekeeper` where postcode IN (select postcode from  `postcodes` 
                        where longitude = '$longitude_list[$index]'
                                and lat = '$lat_list[$index]')";                       
    $postcode_query = $db->query($get_postcode);

    if ($postcode_query->num_rows > 0 ) {
        // output data of each row
        while($row1 = $postcode_query->fetch_assoc()) {
          $bad_wea_post =  $row1["postcode"];
        
        }
    }
    if ($min_temp<10 | $max_temp>=41) {
        $get_temp = " update  `beekeeper` 
        set      max_temp = '$max_temp' , min_temp = '$min_temp'
        where  postcode = '$bad_wea_post'";
        $update_temp = $db->query($get_temp);

    }

    

}

$email= "beenefit@outlook.com.au";  /*****ENTER_FROM_EMAIL_ADDRESS*****/
$name= "Beenefit";				/*****ENTER_A_NAME*****/
$body= "Hello!! Beekeepers"."<br>"." The bad weather is on its way for".$bad_wea_post.". "."<br>"." For tomorrow, predicted Max Temp.  = $max_temp and Min Temp. = $min_temp. So you are advised to keep your Bees and Beehives safe."."<br>"." If you want learn about how to keep Bees safe in bad weather you can visit Beenefit.studio.";
$subject= " Weather Alert.";

$headers= array(

    'Authorization: Bearer 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',  /*****ENTER_YOUR_API_KEY*****/
    'Content-Type: application/json'
);


$get_eamil= "select email, postcode, max_temp, min_temp 
            from `beekeeper`
            where min_temp != 0 ";

$email_query = $db->query($get_eamil);

if ($email_query->num_rows > 0) {
    // output data of each row
    while($row = $email_query->fetch_assoc()) {

    $body= "Hello!! Beekeepers"."<br>"." The bad weather is on its way for ".$row["postcode"].". "."<br>"." For tomorrow, predicted Max Temp.  = ".$row["max_temp"]." and Min Temp. = ".$row["min_temp"].". So you are advised to keep your Bees and Beehives safe."."<br>"." If you want learn about how to keep Bees safe in bad weather you can visit Beenefit.studio.";
        
    $data = array(

        "personalizations" => array(

            array(

                "to" =>array(

                    array(

                        "email" =>$row["email"], /*****ENTER_TO_EMAIL_ADDRESS*****/
                        "name"  => "Beekeepers"
                    )
                )
            )

        ),


        "from" => array(

            "email"=> $email
        ),


        "subject" =>$subject,
        "content" =>array(

                array(

                    "type" => "text/html",
                    "value" => $body
                )
        )


);


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.sendgrid.com/v3/mail/send");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);

curl_close($ch);

echo $response;
    }
} else {
    echo "0 results";
}


    
    

/*$set_null = " update  `beekeeper` 
        set max_temp = NULL , min_temp = NULL";
$update_NULL= $db->query($set_null);   */
        
        
        
        


 ?>







