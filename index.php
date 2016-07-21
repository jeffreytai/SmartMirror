<!-- Located in /Library/WebServer/Documents -->

<?php
require 'class.iCalReader.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Smart Mirror</title>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="initial-scale=1.0">
  <!-- refresh page every 45 seconds -->
  <!-- <meta http-equiv="refresh" content="45" /> -->
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" type="text/css" href="css/weather-icons.css">
  <link rel="stylesheet" type="text/css" href="fonts/roboto.css">
  <script type="text/javascript" src="js/clock.js" async></script>
  <script type="text/javascript" src="js/weather.js" async></script>
  <!-- <script type="text/javascript" src="js/rssfeed.js" async defer></script> -->

  <script src="http://maps.googleapis.com/maps/api/js" async></script>
  <script>
    function initMap() {
      // Instantiate a directions service
      var directionsService = new google.maps.DirectionsService;

      // Create a map and center it
      var mapProp = {
        center:new google.maps.LatLng(34.067342, -118.453143),
        zoom:12,
        // mapTypeId:google.maps.MapTypeId.ROADMAP,
        mapTypeControl:false,
        disableDefaultUI:true
      };

      // Create a renderer for directions and bind it to the map.
      var directionsDisplay = new google.maps.DirectionsRenderer({map: map});

      directionsService.route({
        origin: '540 Kelton Ave, Los Angeles, CA 90024',
        destination: '215 W 6th St, Los Angeles, CA 90014',
        travelMode: google.maps.TravelMode.DRIVING
      }, function(response, status) {
        if (status === google.maps.DirectionsStatus.OK) {
          directionsDisplay.setDirections(response);
        }
      })
      var map=new google.maps.Map(document.getElementById("map"),mapProp);
    }
    google.maps.event.addDomListener(window, 'load', initMap);
  </script>

</head>
<body>

  <div class="region fullscreen below"><div class="container"></div></div>
  <div class="region top bar">
    <div class="container"></div>
    <div class="region top left">
      <div class="container">
        <div id="date" class="small normal"></div>
        <div id="time" class="medium bright"></div>
        <br/>
        <div class="xsmall">
          <div class="normal">Calendar</div>
          <hr>
        </div>

        <!-- Calendar php script -->
        <?php include('php/calendar.php'); ?>
      </div>
    </div>
    <div class="region top center"><div class="container"></div></div>
    <div class="region top right">
      <div class="container">
        <div id='weather' class="small">
          <i class="wi wi-strong-wind"></i>
        </div>
        <div id='temperature' class='bright'></div>
        <br/>
        <div class="xsmall">
          <div>Weather Forecast</div>
          <hr>
          <table class="small" id="forecast">
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="region upper third"><div class="container"></div></div>
  <div class="region middle center">
    <div class="container">
      <div id="map"></div>
    </div>
  </div>
	<div class="region lower third"><div class="container"><br/></div></div>

  <div class="region bottom bar">
		<div class="container"></div>
		<div class="region bottom left"><div class="container"></div></div>
		<div class="region bottom center">
      <div class="container">
        <div id="rssfeed" class="xsmall dimmed"></div>

        <!-- RSS news feed from New York Times -->
        <?php include('php/rssfeed.php') ?>

        <!-- JavaScript for RSS -->
        <script type="text/javascript">
        var news = <?php echo json_encode($news) ?>;
        var rssfeed = document.getElementById('rssfeed');
        rssfeed.innerHTML = news[0][0];
        setInterval(change, 7000);
        var counter = 1;
        function change() {
          rssfeed.innerHTML = news[counter][0];
          counter++;
          if (counter >= news.length) counter = 0;
        }

        </script>

      </div>
    </div>
		<div class="region bottom right"><div class="container"></div></div>
	</div>
  <div class="region fullscreen above"><div class="container"></div></div>

</body>

</html>
