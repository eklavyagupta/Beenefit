<?php

$user ="azure";
$pass ='6#vWHD_$';
$db ="localdb";

$db =new mysqli('127.0.0.1:50190', $user, $pass, $db) or die ("Unable to connect");




$search_value=$_POST["search"];


if (strlen($search_value) == 4){
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

if($air_qi == 1){
  $airsituation = "Good";
}

if($air_qi == 2){
  $airsituation = "Fair";
}

if($air_qi == 3){
  $airsituation = "Moderate";
}

if($air_qi == 4){
  $airsituation = "Poor";
}

if($air_qi == 5){
  $airsituation = "Very Poor";
}





} 


else {echo "<script>alert('Not correct postcode'); location.href = 'index.html'</script>";
}


}
else {echo "<script>alert('Not correct postcode'); location.href = 'index.html'</script>";
}




?> 





<!DOCTYPE html>
<html lang="en-US" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Beenefit</title>


    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicons/favicon.ico">
    <link rel="manifest" href="assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="assets/img/favicons/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">


    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link href="assets/css/theme.css" rel="stylesheet" />

<style>    


.report-container {
    border: #E0E0E0 1px solid;
    padding: 20px 40px 40px 40px;
    border-radius: 2px;
    width: 550px;
    margin: 0 auto;
}

.weather-icon {
    
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


    <main class="main" id="top">
      <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3 backdrop" data-navbar-on-scroll="data-navbar-on-scroll">
        <div class="container"><a class="navbar-brand d-flex align-items-center fw-bolder fs-2 fst-italic" href="index.html">
            <div class="text-warning">BEENEFIT</div>
          </a>
          <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
          <div class="collapse navbar-collapse border-top border-lg-0 mt-4 mt-lg-0" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto pt-2 pt-lg-0">
              <li class="nav-item px-2"><a class="nav-link fw-medium active" aria-current="page" href="index.html">Home</a></li>
              <li class="nav-item px-2"><a class="nav-link fw-medium" href="timeline.html">Timeline</a></li>
              <li class="nav-item px-2"><a class="nav-link fw-medium" href="weather.html">Discovery</a></li>
            </ul>
          </div>
        </div>
      </nav>

      </br>
      </br>




        <section class="py-5">
        <div class="bg-holder d-none d-sm-block" style="background-image:url(assets/img/illustrations/category-bg.png);background-position:right top;background-size:200px 320px;">
        </div>
        
        <!--/.bg-holder-->

        <div class="container">
          <div class="row flex-center">

            <div class="col-md-5 order-md-0 text-center text-md-start">
              <h1><b><?php echo $suburb; ?></b></h1>
              <div class="time">
                  <div><h3><?php echo  $dateInLocal; ?></h3></div>
                </div>
                
                <div class="weather-forecast">
                    <h3>
                    <img src="http://openweathermap.org/img/wn/<?php echo $weather_icon; ?>@4x.png" class="weather-icon" style="vertical-align:middle" /> 
                        <?php echo $temperature; ?>°C</h3>
                        <span class="min-temperature"><h3><?php echo $weather; ?></h3></span>
                </div>
                </br>
                <div class="time">
                    <div><h4>Humidity: <?php echo $humidity; ?> %</h4></div>
                    <div><h4>Wind: <?php echo $wind_speed; ?> m/s</h4></div>
                    <div><h4>Air quality:  <?php echo $airsituation; ?></h4></div>
                </div>
            </div>

            <div class="col-md-5 text-center text-md-start">
              <h1><b><p style="color:#ffaa00">Suggestion: </p></b></h1>
                <h4> 
                  <?php
                      if ($temperature > 10 & 
                          $temperature <= 35 &
                          $air_qi <= 2 &
                          $main_weather != "Rain" & $main_weather != "Thunderstorm" & $main_weather != "Drizzle" &
                          $humidity >= 75 &
                          $humidity <= 80 &
                          $wind_speed < 5.5
                          ){
                            echo "Bees are vey safe now :)";

                      }
                      else {
                        if ($temperature <= 10) {
                          echo"· Build hives in almond plantations where temperatures are warmer"."<br>".
                        "· Add a little insulation to the outside of their hives"."<br>".
                        "· Make sure there aren't drafty holes in their equipment that let cold air or, worse, water in."."<br>".
                        "· Hives are distributed evenly throughout the orchard individually"."<br>";
                          
                        } 
  
                        elseif ($temperature > 35 & $temperature < 37){
                          echo "·Don’t open hive lip to do hive management"."<br>".
                          "· Spraying water on the external walls of the hive"."<br>";
                        }
                        elseif ($temperature > 37 ){
                          echo "· Hive construction with adequate air circulation"."<br>";
                        }
                        
  
  
  
                        # Air quality suggestion
  
  
  
                        if ($air_qi >= 3) {
                          echo"· Move the hive inside."."<br>";
                          
                        } 
  
  
  
                        # Rain suggestion
  
                        if ($main_weather == "Rain"|$main_weather == "Thunderstorm" |$main_weather == "Drizzle") {
                          echo "· Build a full hive"."<br>"." · Hives are distributed evenly throughout the orchard individually."."<br>";
                          
                        } 
  
  
  
  
  
  
                        # Humidity suggestion
  
  
                        if ($humidity >80 |$humidity<75) {
                          echo"· keep 75%-80% humidity in winter"."<br>";
                          
                        }
  
  
  
                        # Wind Speed suggestion
  
  
                        if ($wind_speed > 5.5) {
                          echo "· Place bees in places with frequent winds"."<br>"." · Ensure The location of the bee farm should be set at the downwind of the honey powder source, so that the bees will go against the wind when they are out of the nest without load.";
                          
                        }
                      }
                      

                    ?>
                </h4>

            </div>
          </div>
        </div>
      </section>
      
            <!-- ============================================-->
      <!-- <section> begin ============================-->
      <section class="py-8">
        <div class="container">
          <div class="row flex-center">
            <div class="col-md-5 order-md-1 text-center text-md-end"><img class="img-fluid mb-4" src="assets/img/illustrations/feature.png" width="450" alt="" /></div>
            <div class="col-md-5 text-center text-md-start">
              <h6 class="fw-bold fs-2 fs-lg-3 display-3 lh-sm">Subscribe Us</h6>
              <p class="my-4 pe-xl-8">Enter your Email and Postcode to get Latest Bee Alerts.</p>
              <div class="col-lg-6 d-flex justify-content-lg-end justify-content-center">
                <form class="row row-cols-lg-auto g-0 align-items-center" action = "email.php"  method="POST">
                  <div class="col-9 col-lg-8">
                    <label class="visually-hidden" for="colFormLabel">Username</label>
                    <div class="input-group">
                      <input class="rounded-end-0 form-control" id="colFormLabel" type="email" name= "email" placeholder="Email" />
                    </div>
                    </br>
                    <div class="input-group">
                      <input class="rounded-end-0 form-control" id="colFormLabel" type="text" name= "postcode" placeholder="Postcode" />
                    </div>
                  </div>
                  <div class="col-3 col-lg-4">
                    <button class="btn btn-primary rounded-start-0" type="submit">Submit</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- end of .container-->
        <!-- end of .container-->
      </section>
      <!-- <section> close ============================-->
      <!-- ============================================-->


      <section class="py-0 bg-primary-gradient">
        <div class="bg-holder" style="background-image:url(assets/img/illustrations/footer-bg.png);background-position:center;background-size:cover;">
        </div>
        <!--/.bg-holder-->

        <div class="container">
          <div class="row flex-center py-8">
            <div class="col-lg-6 d-flex justify-content-lg-end justify-content-center">
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-auto mb-2">
              <p class="mb-0 fs--1 text-white my-2 text-center">&copy; This template is made with&nbsp;
                <svg class="bi bi-suit-heart-fill" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#1F3A63" viewBox="0 0 16 16">
                  <path d="M4 1c2.21 0 4 1.755 4 3.92C8 2.755 9.79 1 12 1s4 1.755 4 3.92c0 3.263-3.234 4.414-7.608 9.608a.513.513 0 0 1-.784 0C3.234 9.334 0 8.183 0 4.92 0 2.755 1.79 1 4 1z"></path>
                </svg>&nbsp;by&nbsp;<a class="text-white" href="https://themewagon.com/" target="_blank">ThemeWagon </a>
                 <div>Icons made by <a href="https://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div> 
                <div> Presented by <a href="https://www.beenefit.studio" title="BEENEFIT">BEENEFIT</a></div>
              </p>
            </div>
          </div>
        </div>
      </section>





    






    </main>
<!-- DOCUMENT WRAPPER ENDS -->

    <script src="vendors/@popperjs/popper.min.js"></script>
    <script src="vendors/bootstrap/bootstrap.min.js"></script>
    <script src="vendors/is/is.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="assets/js/theme.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400&amp;display=swap" rel="stylesheet">

</body>

</html>
