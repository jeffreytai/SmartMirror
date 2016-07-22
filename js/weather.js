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

function displayWeather() {
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
  document.getElementById('temperature').innerHTML += Math.round(weather.main.temp) + '\xB0';

  // Weather forecast
  var forecast_url = 'http://api.openweathermap.org/data/2.5/forecast/daily?zip=90024,us&units=imperial&appid=';
  var forecast = JSON.parse(retrieve(forecast_url));
  // console.log(forecast);

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
    high_cell.innerHTML = Math.round(high_temp);
    row.appendChild(high_cell);

    var low_cell = document.createElement('td');
    var low_temp = forecast.list[i].temp.min;
    low_cell.innerHTML = Math.round(low_temp);
    row.appendChild(low_cell);

  }
}

window.addEventListener('load', displayWeather);
