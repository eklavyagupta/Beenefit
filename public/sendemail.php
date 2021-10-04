<?php
// login info
$user ="azure";
$pass ='6#vWHD_$';
$db ="localdb";

$db =new mysqli('127.0.0.1:50190', $user, $pass, $db) or die ("Unable to connect");

// sql query
$weather_check= mysqli_query($db,"select lat, longitude from `beekeeper`b,`postcodes`p where p.postcode = b.postcode");

// save the list of longitude and latitude
while($row = mysqli_fetch_array($weather_check)) {
    $lat_list[] = $row['lat'];
    $longitude_list[] = $row['longitude'];
 }

 // for loop to get the variable for each longitude and latitude
foreach( $lat_list as $index => $lat ) {

    // set the api based on longitude and latitude
    $ApiUrl ='https://api.openweathermap.org/data/2.5/onecall?lat='.$lat_list[$index].'&lon='.$longitude_list[$index].'&exclude=current,minutely,hourly,alerts&appid=f8602df67c495efda45f5097b5244ba6&units=metric';
    
    // run api
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $ApiUrl);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    curl_close($ch);

    // save the respone of api
    $data = json_decode($response);


    // save the diferent value in the respone
    $Time = $data ->daily[1] ->dt;
    $dateInLocal = date("Y-m-d", $Time);
    $main_weather_id = $data ->current ->weather[0]->id;
    $main_weather = $data ->current ->weather[0]->main;
    $min_temp = $data ->daily[1] ->temp->min;
    $max_temp = $data ->daily[1] ->temp->max;
    
    
    // match the postcode in table beekeepers
    $get_postcode = "  select distinct postcode from `beekeeper` where postcode IN (select postcode from  `postcodes` 
                        where longitude = '$longitude_list[$index]'
                                and lat = '$lat_list[$index]')";                       
    $postcode_query = $db->query($get_postcode);

    // get the postcode which have the bad weather for beekeeping
    if ($postcode_query->num_rows > 0 ) {
        // output data of each row
        while($row1 = $postcode_query->fetch_assoc()) {
          $bad_wea_post =  $row1["postcode"];
        
        }
    }
    // save the bad weather into database
    if ($min_temp<10 | $max_temp>=41) {
        $get_temp = " update  `beekeeper` 
        set      max_temp = '$max_temp' , min_temp = '$min_temp'
        where  postcode = '$bad_wea_post'";
        $update_temp = $db->query($get_temp);

    }
    if ($main_weather_id >= 502 | $main_weather_id <= 512) {
        $set_rain = " update  `beekeeper` 
        set      Rain = Y 
        where  postcode = '$bad_wea_post'";
        $update_rain = $db->query($set_rain);

    }

    

}

/*****ENTER_FROM_EMAIL_ADDRESS*****/
$email= "beenefit@outlook.com.au";  
/*****ENTER_A_NAME*****/
$name= "Beenefit";	
/*****ENTER_A_subject*****/			
$subject= " Weather Alert.";

// set the key
$headers= array(

    'Authorization: Bearer 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',  
    'Content-Type: application/json'
);

// create a query to get the postcode where max_temp, min_temp are updated
$get_eamil= "select email, postcode, max_temp, min_temp 
            from `beekeeper`
            where min_temp != NULL";
$email_query = $db->query($get_eamil);


if ($email_query->num_rows > 0) {
    // output data of each row
    while($row = $email_query->fetch_assoc()) {
    // the body of email
    $body= "Hello!! Beekeepers"."<br>"." The bad weather is on its way for ".$row["postcode"].". "."<br>"." For tomorrow, predicted Max Temp.  = ".$row["max_temp"]." and Min Temp. = ".$row["min_temp"].". So you are advised to keep your Bees and Beehives safe."."<br>"." If you want learn about how to keep Bees safe in bad weather you can visit Beenefit.studio.";
    // save the content (receiver, body) of email
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

// send the eamil
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
} 


// create a query to get the postcode where Rain are updated
$get_eamil_rain= "select email, postcode, Rain 
            from `beekeeper`
            where min_temp != NULL ";

$email_query_rain = $db->query($get_eamil_rain);

if ($email_query_rain->num_rows > 0) {
    // output data of each row
    while($row = $email_query_rain->fetch_assoc()) {

    $body= "Hello!! Beekeepers"."<br>"." The bad weather is on its way for ".$row["postcode"].". "."<br>"." For tomorrow, predicted weather is ".$main_weather.". So you are advised to keep your Bees and Beehives safe."."<br>"." If you want learn about how to keep Bees safe in bad weather you can visit Beenefit.studio.";
        
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

// send the email with different weather condition alert.
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
} 

else {
    echo "0 results";
}


    
    
// reset the column into NULL after sending the email
$set_null = " update  `beekeeper` 
        set max_temp = NULL , min_temp = NULL, Rain = NULL";
$update_NULL= $db->query($set_null);   
        
        
        
        


 ?>







