<?php

$dsn = 'mysql:dbname=csv_db;host=127.0.0.1:55937;charset=utf8';
$user = 'azure';
$password = '6#vWHD_$';

try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

echo "Success: A proper connection to MySQL was made!";


echo"great work!!";

echo '<br>';

echo '<style media="screen" type="text/css">@font-face{font-family:FontAwesome;src:url(/css/fonts/fontawesome-webfont);font-weight:400;font-style:normal}.fa{display:inline-block;font:normal normal normal 14px/1 FontAwesome;font-size:inherit;text-rendering:auto;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.fa-angle-double-up:before{content:"\f102"}</style>';

echo '<br>';

$search_value=$_POST["search"];

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

echo 'Postcode: '.$postcode. "<br>";
echo 'Suburb: '.$suburb. "<br>"; 





$sql1= "select longitude from `postcodes` where postcode = '$search_value'";

$lo = $db->query($sql1);

$sql2= "select lat from `postcodes` where postcode = '$search_value'";

$la = $db->query($sql2);




if ($lo->num_rows > 0 ) {
    // output data of each row
    while($row1 = $lo->fetch_assoc()) {
      $new_long =  $row1["longitude"];
      echo  " - long: " . $row1["longitude"]. "<br>";
    }
  } else {
    echo "0 results";
  }


if ($la->num_rows > 0) {
  // output data of each row
  while($row = $la->fetch_assoc()) {
    $new_lat =  $row["lat"];
    echo  " - lat: " . $row["lat"]. "<br>";
  }
} else {
  echo "0 results";
}

echo '<br>';


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


echo '<br>';
$Time = $data ->current ->dt;
$temperature = $data ->current ->temp;
$humidity = $data -> current ->humidity;
$wind_speed = $data ->current ->wind_speed;
$weather = $data ->current ->weather[0]->description;
$main_weather = $data ->current ->weather[0]->main;
$dateInLocal = date("Y-m-d", $Time);


echo 'Time: '.$dateInLocal. "<br>";
echo 'temperature: '.$temperature.'°C'. "<br>";
echo 'humidity: '.$humidity.'%'. "<br>"; 
echo 'wind speed: '.$wind_speed.' m/s'. "<br>";
echo 'weather: '.$weather. "<br>";


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

echo '<br>';
echo 'Air quality: '.$air_qi. "<br>";

# temperature suggestion
echo '<br>';

if ($temperature <= 10) {
  echo "Temperature Suggestion: "."<br>";
  echo "·Build hives in almond plantations where temperatures are warmer"."<br>";
  echo "· Cold area: add a little insulation to the outside of their hives"."<br>";
  echo "· Most beekeepers don't need to do anything "."<br>";
  echo "· Make sure there aren't drafty holes in their equipment that let cold air or, worse, water in."."<br>";
  echo "· Hives are distributed evenly throughout the orchard individually";
  
} 

elseif ($temperature > 35 & $temperature < 37){
  echo "Temperature Suggestion: "."<br>";
  echo "·Don’t open hive lip to do hive management"."<br>";
  echo "·Spraying water on the external walls of the hive"."<br>";
}
elseif ($temperature > 37 ){
  echo "Temperature Suggestion: "."<br>";
  echo "·Hive construction with adequate air circulation"."<br>";
}
else {
  echo "No suggestion, good temperature!";
}

echo '<br>';

# Air quality suggestion
echo '<br>';

if ($air_qi >= 3) {
  echo "Air quality Suggestion: "."<br>";
  echo "Move the hive inside.";
  
} 
else {
  echo "No suggestion, good air quality!";
}

echo '<br>';


# Rain suggestion
echo '<br>';

if ($main_weather == "Rain"|$main_weather == "Thunderstorm" |$main_weather == "Drizzle") {
  echo "Weather Suggestion: "."<br>";
  echo "· Build a full hive"."<br>";
  echo "· Hives are distributed evenly throughout the orchard individually.";
  
} 
else {
  echo "No suggestion, good weather!";
}

echo '<br>';




# Humidity suggestion
echo '<br>';

if ($humidity >80 |$humidity<75) {
  echo "Humidity Suggestion: "."<br>";
  echo "keep 75%-80% humidity in winter";
  
} 
else {
  echo "No suggestion, Suitable humidity!";
}

echo '<br>';

# Wind Speed suggestion
echo '<br>';

if ($wind_speed > 5.5) {
  echo "Wind Speed Suggestion: "."<br>";
  echo "· Place bees in places with frequent winds. "."<br>";
  echo "· Ensure The location of the bee farm should be set at the downwind of the honey powder source, so that the bees will go against the wind when they are out of the nest without load.";
  
} 
else {
  echo "No suggestion, Suitable wind speed!";
}

?> 