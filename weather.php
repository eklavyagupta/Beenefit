<?php

$user ="azure";
$pass ='6#vWHD_$';
$db ="csv_db";

$db =new mysqli('127.0.0.1:55937', $user, $pass, $db) or die ("Unable to connect");


//echo"great work!!";



//echo"great work!!";


$search_value=$_POST["search"];

$sql1= "select longitude from `postcodes` where postcode = '$search_value'";

$lo = $db->query($sql1);

$sql2= "select lat from `postcodes` where postcode = '$search_value'";

$la = $db->query($sql2);

if ($lo->num_rows > 0 ) {
  // output data of each row
  while($row1 = $lo->fetch_assoc()) {
    $new_long =  $row1["longitude"];
   // echo  " - long: " . $row1["longitude"]. "<br>";
  }
    // output data of each row
  while($row = $la->fetch_assoc()) {
    $new_lat =  $row["lat"];
   // echo  " - lat: " . $row["lat"]. "<br>";
  }
  $SuburbApi = 'http://api.openweathermap.org/geo/1.0/zip?zip='.$search_value.',au&appid=f8602df67c495efda45f5097b5244ba6';


$ch = curl_init();

curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $SuburbApi);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$sub_response = curl_exec($ch);

curl_close($ch);
$Sub_data = json_decode($sub_response);

$postcode = $Sub_data ->zip;
$suburb = $Sub_data ->name;







$ApiUrl ='https://api.openweathermap.org/data/2.5/onecall?lat='.$new_lat.'&lon='.$new_long.'&exclude=minutely,hourly,daily,alerts&appid=f8602df67c495efda45f5097b5244ba6&units=metric';

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


//echo '<br>';
$Time = $data ->current ->dt;
$temperature = $data ->current ->temp;
$humidity = $data -> current ->humidity;
$wind_speed = $data ->current ->wind_speed;
$weather = $data ->current ->weather[0]->description;
$main_weather = $data ->current ->weather[0]->main;
$dateInLocal = date("Y-m-d", $Time);
$weather_icon =$data ->current ->weather[0]->icon;

/*
echo 'Time: '.$dateInLocal. "<br>";
echo 'temperature: '.$temperature.'°C'. "<br>";
echo 'humidity: '.$humidity.'%'. "<br>"; 
echo 'wind speed: '.$wind_speed.' m/s'. "<br>";
echo 'weather: '.$weather. "<br>";
*/

$air_api = 'http://api.openweathermap.org/data/2.5/air_pollution?lat='.$new_lat.'&lon='.$new_long.'&appid=f8602df67c495efda45f5097b5244ba6';

$ch = curl_init();

curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $air_api);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$air_response = curl_exec($ch);

curl_close($ch);
$air_data = json_decode($air_response);
$air_qi = $air_data ->list[0] ->main ->aqi;

//echo '<br>';
//echo 'Air quality: '.$air_qi. "<br>";

# temperature suggestion
//echo '<br>';

if ($temperature <= 10) {
  $temp_suggestion = "·Build hives in almond plantations where temperatures are warmer"."<br>".
 "· Add a little insulation to the outside of their hives"."<br>".
"· Make sure there aren't drafty holes in their equipment that let cold air or, worse, water in."."<br>".
 "· Hives are distributed evenly throughout the orchard individually";
  
} 

elseif ($temperature > 35 & $temperature < 37){
  $temp_suggestion = "·Don’t open hive lip to do hive management"."<br>".
  "·Spraying water on the external walls of the hive";
}
elseif ($temperature > 37 ){
  $temp_suggestion = "· Hive construction with adequate air circulation";
}
else {
  $temp_suggestion = "· Temperature: Suitable !";
}



# Air quality suggestion


if ($air_qi >= 3) {
  $air_qi_suggestion =  "· Move the hive inside.";
  
} 
else {
  $air_qi_suggestion = "· Air quality: GOOD!";
}



# Rain suggestion

if ($main_weather == "Rain"|$main_weather == "Thunderstorm" |$main_weather == "Drizzle") {
  $weather_suggestion = "· Build a full hive"."<br>"." · Hives are distributed evenly throughout the orchard individually.";
  
} 
else {
  $weather_suggestion = "· Weather: GOOD!";
}






# Humidity suggestion


if ($humidity >80 |$humidity<75) {
  $humi_suggestion = "· keep 75%-80% humidity in winter";
  
} 
else {
  $humi_suggestion = "· Humidity: Suitable!";
}



# Wind Speed suggestion


if ($wind_speed > 5.5) {
  $wind_suggestion = "· Place bees in places with frequent winds"."<br>"." · Ensure The location of the bee farm should be set at the downwind of the honey powder source, so that the bees will go against the wind when they are out of the nest without load.";
  
} 
else {
  $wind_suggestion =  "· Wind speed: Suitable!";
}











} 


else {echo "<script>alert('Not correct postcode'); location.href = 'about.html'</script>";
}




?> 





<!DOCTYPE html>
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en-US"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en-US"> <![endif]-->
<!--[if gt IE 8]><!-->
<html lang="en">


<head>

    <!--meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--meta tags ends-->

    <title>Beenifit</title>
    <!-- Links to logo fonts -->
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Bangers" />
    
    <!--- Links to google fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,800%7cRoboto+Mono:400,700%7cMerriweather:300%7cAbril+Fatface'
          rel='stylesheet' >
    <!-- Links to fonts ends -->

    <!-- Bootstrap stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Icons -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <!-- Popup Images -->
    <link rel="stylesheet" type="text/css" href="css/magnific-popup.css">

    <!-- css animation -->
    <link rel="stylesheet" type="text/css" href="css/animate.css">

    <!-- custom stylesheets -->
    <link rel="stylesheet" type="text/css" href="css/main.css">


    <!-- font-awesome icons -->
    <link href="fontawesome.css" rel="stylesheet"> 
    <link href="//fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Abril+Fatface" />
    <link href='https://fonts.googleapis.com/css?family=Caveat' rel='stylesheet'>
    
    <style>


.report-container {
    border: #E0E0E0 1px solid;
    padding: 20px 40px 40px 40px;
    border-radius: 2px;
    width: 550px;
    margin: 0 auto;
}

.weather-icon {
    vertical-align: middle;
    margin-right: 20px;
}

.weather-forecast {
    color: #212121;
    font-size: 1.2em;
    font-weight: bold;
    margin: 20px 0px;
}

span.min-temperature {
    margin-left: 15px;
    color: #929292;
}

.time {
    line-height: 25px;
}
</style>
    

</head>

<body>

<!-- Start: Preloader section -->
<div id="loader-wrapper">
    <div id="loader"></div>
</div>
<!-- End: Preloader section -->

<!-- DOCUMENT WRAPPER STARTS -->
    <main>


        <!-- MAIN HEADER STARTS-->
        <header id="header">

            <!-- TOP NAVIGATION -->
            <nav class="top-navigation-bar navbar navbar-default navbar-fixed-top">
                <div class="container">
                <div class="row">
                <div class="col-md-12">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" 
                                data-toggle="collapse" data-target="#top-navigation-bar" 
                                aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">BEENEFIT<span class="thin"></span></a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="top-navigation-bar">
                        <ul class="nav navbar-nav navbar-right">
                            <li class="active"><a href="index.html">Home</a></li>
                            <li><a href="about.html">Bee Aware of Weather</a></li>
                            <!-- <li><a href="contact.html">Get in Touch</a></li>                             -->
                        </ul>
                    </div>
                    <!-- /.navbar-collapse -->
                    </div>
                    </div>
                </div>
                <!-- /.container-->
            </nav>
            <!-- TOP NAVIGATION ENDS -->

        </header>
        
        <!-- main header ends-->




        <section id="address" class="address page-top">
            <div class="container">
                <div class="row">
                    
                        <div class="col-md-6">
                        <div class="address-wrapper wow fadeInUp" data-wow-delay="0.3s">
                        <div class = "report-container">
                          <h1><?php echo $suburb; ?></h1>
                          <h1><?php echo $temperature; ?>°C</h1>
                          <div class="time">
                              <div><?php echo  $dateInLocal; ?></div>
                          </div>
                          <div class="weather-forecast">
                                <img
                                    src="http://openweathermap.org/img/w/<?php echo $weather_icon; ?>.png"
                                    class="weather-icon" /> <span
                                    class="min-temperature"><h12><?php echo $weather; ?></h12></span>
                            </div>
                          <div class="time">
                              <div>Humidity: <h12><?php echo $humidity; ?> %</h12></div>
                              <div>Wind: <h12><?php echo $wind_speed; ?> m/s</h12></div>
                              <div>Air quality: <h12> <?php echo "level ". $air_qi; ?></h12></div>
                          </div>
                        </div>
                            </div>
                    </div>
                    <div class="col-md-6">
                    <div class="map-wrapper wow fadeInUp" data-wow-delay="0.6s">
                      <h2> Suggestion: <h1>
                    <h3> <?php echo $temp_suggestion ."<br>"; ?></h2>
                    <h3> <?php echo $air_qi_suggestion ."<br>"; ?></h2>
                    <h3> <?php echo $humi_suggestion ."<br>"; ?></h2>
                    <h3> <?php echo $weather_suggestion; ?></h2>
                    <h3> <?php echo $wind_suggestion; ?></h2>
                                         
                    </div>
                    </div>
                    
                    
            </div>
            </div>
        </section>

       


    </main>
<!-- DOCUMENT WRAPPER ENDS -->



<!-- SCRIPTS -->

    <!-- jQuery (necessary for all the plugins) -->
    <script src="js/jquery-1.11.2.min.js"></script>

    <script src="http://maps.google.com/maps/api/js"></script>
    <script src="js/gmaps.js"></script>
    <script type="text/javascript" src="js/jquery.magnific-popup.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.min.js"></script>
    <script type="text/javascript" src="js/wow.min.js"></script>
    <script type="text/javascript" src="js/jquery.validate.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>

<!-- SCRIPTS ENDS -->
</body>

</html>
