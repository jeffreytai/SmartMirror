<!-- Located in /Library/WebServer/Documents -->

<?php
require 'class.iCalReader.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Smart Mirror</title>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <!-- refresh page every 30 seconds -->
  <meta http-equiv="refresh" content="30" />
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" type="text/css" href="css/weather-icons.css">
  <link rel="stylesheet" type="text/css" href="fonts/roboto.css">
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

        <?php

        $ical = new ical('Calendar.ics');
        $current_date = date_create();

        // TODO: Doesn't get today's events that have already passed

        function getDateTimeObject($str) {
          // formats events that have a set time
          $date = datetime::createfromformat('Ymd\TGis', $str);
          // all-day events
          if (!$date) $date = datetime::createfromformat('Ymd', $str);
          return $date;
        }

        function same_day($first, $second) {
          $diff = date_diff($first, $second);
          // same day
          if ($diff->format('%a') === '0') return true;
          // different day
          else return false;
        }

        function same_day_callback($event) {
          $date = getDateTimeObject($event['DTSTART']);
          return same_day(date_create(), $date);
        }

        function date_compare($a, $b)
        {
            $t1 = strtotime($a['DTSTART']);
            $t2 = strtotime($b['DTSTART']);
            return $t1 - $t2;
        }

        // Filters calendar for future events, leaves as unordered
        $future_events = array();
        foreach($ical->events() as $event) {
          $date = getDateTimeObject($event['DTSTART']);
          if ($date >= $current_date) {
            array_push($future_events, $event);
          }
        }

        // Orders future events by datetime
        usort($future_events, 'date_compare');

        // Finds today's events
        $today_events = array_filter($future_events, 'same_day_callback');
        echo "<div class='xsmall'>Today: <br/>";
        foreach($today_events as $event) {
          // displays 'All day' before event
          if (!datetime::createfromformat('Ymd\TGis', $event['DTSTART']))
            echo '<i>All day - </i>';
          else {
            // TODO: confirm this works
            $date = getDateTimeObject($event['DTSTART']);
            $time = date_format($date, 'g:i A');
            echo '<i>'.$time.' - </i>';
          }
          print_r($event['SUMMARY']);
          echo '<br/>';
        }
        echo "</div>\r\n<br/>";

        $upcoming_events = array_diff(array_map('serialize',$future_events), array_map('serialize',$today_events));
        $upcoming_events = array_map('unserialize',$upcoming_events);

        echo "<div class='xsmall'>Upcoming Events: <br/>";
        foreach(array_slice($upcoming_events, 0, 5) as $event) {
          echo '<i>';
          $date = getDateTimeObject($event['DTSTART']);
          print_r(date_format($date, 'n/d'));
          echo ' - </i>';
          print_r($event['SUMMARY']);
          echo '<br/>';
        }
        echo "</div>\r\n<br/>";
        ?>
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
        <script>

        function retrieve(url) {
          var api_key = '6248f786387209c365d62b6341efe73b';
          var httpreq = new XMLHttpRequest();
          httpreq.open('GET', url + api_key, false);
          httpreq.send(null);
          return httpreq.responseText;
        }

        var icon_table = {
          '01d': 'wi-day-sunny',
          '02d': 'wi-day-cloudy',
    			'03d': 'wi-cloudy',
    			'04d': 'wi-cloudy-windy',
    			'09d': 'wi-showers',
    			'10d': 'wi-rain',
    			'11d': 'wi-thunderstorm',
    			'13d': 'wi-snow',
    			'50d': 'wi-fog',
    			'01n': 'wi-night-clear',
    			'02n': 'wi-night-cloudy',
    			'03n': 'wi-night-cloudy',
    			'04n': 'wi-night-cloudy',
    			'09n': 'wi-night-showers',
    			'10n': 'wi-night-rain',
    			'11n': 'wi-night-thunderstorm',
    			'13n': 'wi-night-snow',
    			'50n': 'wi-night-alt-cloudy-windy'
        };

        var weekday_table = {
          0: 'Sunday',
          1: 'Monday',
          2: 'Tuesday',
          3: 'Wednesday',
          4: 'Thursday',
          5: 'Friday',
          6: 'Saturday'
        };

        // Wind, sunrise/sunset, temperature
        var current_weather_url = 'http://api.openweathermap.org/data/2.5/weather?zip=90024,us&units=imperial&appid=';
        var weather = JSON.parse(retrieve(current_weather_url));
        // console.log(weather);
        document.getElementById('weather').innerHTML += Math.round(weather.wind.speed*10)/10;

        var sunrise = (new Date(weather.sys.sunrise*1000)).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }).replace("AM","am").replace("PM","pm");
        var sunset = (new Date(weather.sys.sunset*1000)).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }).replace("AM","am").replace("PM","pm");
        document.getElementById('weather').innerHTML += '  <i class="wi wi-sunrise"></i>  ';
        document.getElementById('weather').innerHTML += sunrise;
        document.getElementById('weather').innerHTML += '  <i class="wi wi-sunset"></i>  ';
        document.getElementById('weather').innerHTML += sunset;

        var icon = icon_table[weather.weather[0].icon];
        document.getElementById('temperature').innerHTML = '<i class="wi ' + icon + '"></i> ';
        document.getElementById('temperature').innerHTML += Math.round(weather.main.temp*10)/10 + '\xB0';

        // Weather forecast
        var forecast_url = 'http://api.openweathermap.org/data/2.5/forecast/daily?zip=90024,us&units=imperial&appid=';
        var forecast = JSON.parse(retrieve(forecast_url));
        console.log(forecast);

        var forecast_table = document.getElementById('forecast');
        for (i=1; i<7; i++) {
          var row = document.createElement('tr');
          var weekday = weekday_table[(new Date(forecast.list[i].dt*1000)).getDay()];
          forecast_table.appendChild(row);

          var weekday_cell = document.createElement('td');
          weekday_cell.className = "day";
          weekday_cell.innerHTML = weekday.substring(0,3);
          row.appendChild(weekday_cell);

          var icon_cell = document.createElement('td');
          var iconName = icon_table[forecast.list[i].weather[0].icon];
          icon_cell.innerHTML = '<i class="wi ' + iconName + '"></i>';
          row.appendChild(icon_cell);

          var high_cell = document.createElement('td');
          var high_temp = forecast.list[i].temp.max;
          high_cell.innerHTML = high_temp.toFixed(1);
          row.appendChild(high_cell);

          var low_cell = document.createElement('td');
          var low_temp = forecast.list[i].temp.min;
          low_cell.innerHTML = low_temp.toFixed(1);
          row.appendChild(low_cell);

        }

        </script>

      </div>
    </div>
  </div>

  <div class="region upper third"><div class="container"></div></div>
  <div class="region middle center"><div class="container"></div></div>
	<div class="region lower third"><div class="container"><br/></div></div>

  <div class="region bottom bar">
		<div class="container"></div>
		<div class="region bottom left"><div class="container"></div></div>
		<div class="region bottom center"><div class="container"></div></div>
		<div class="region bottom right"><div class="container"></div></div>
	</div>
  <div class="region fullscreen above"><div class="container"></div></div>

</body>

<script type="text/javascript">
  var d = new Date();
  var date = d.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
  var hr = d.getHours();
  var min = d.getMinutes();
  var sec = d.getSeconds();
  var ampm;
  hr = (hr == 0) ? 12 : hr;
  if (hr == 0) {
    hr = 12;
    ampm = "am";
  } else if (hr < 12)
    ampm = "am";
  else if (hr == 12)
    ampm = "pm";
  else {
    hr = hr - 12;
    ampm = "pm";
  }
  min = min < 10 ? '0' + min : min;
  var time = hr + ':' + min + ' ' + ampm;
  document.getElementById("date").innerHTML = date;
  document.getElementById("time").innerHTML = time;
</script>

</html>
