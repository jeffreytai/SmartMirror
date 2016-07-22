function initMap() {

  var xmlhttp = new XMLHttpRequest();
  var gmaps_api_key = 'AIzaSyBnMRgsLkIVjd1wVOETpKdHY9XYkqf4lS0';
  var url = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins=34.067297,-118.452532&destinations=34.046519,-118.250408&key=';

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 & xmlhttp.status == 200) {
      var obj = JSON.parse(xmlhttp.responseText);
      var time = obj.rows[0].elements[0].duration.text;
      document.getElementById('travel-time').innerHTML = time;
      document.getElementById('travel-time').innerHTML += ' to work';
      console.log("Reached here");
      console.log(time);
    }
  }
  xmlhttp.open('GET', url + gmaps_api_key, true);
  xmlhttp.send();


  var directionsDisplay;

  // Instantiate a directions service
  var directionsService = new google.maps.DirectionsService;
  var map;

  function initialize() {
    directionsDisplay = new google.maps.DirectionsRenderer;
    var apartment = new google.maps.LatLng(34.067342, -118.452961);
    var mapOptions = {
      zoom: 14,
      center: apartment,
      zoomControl: false,
      streetViewControl: false
    };
    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    directionsDisplay.setMap(map);
    calcRoute();
  }

  function calcRoute() {
    var start = new google.maps.LatLng(34.067342, -118.452961);
    var end = new google.maps.LatLng(34.046670, -118.251095);

    var bounds = new google.maps.LatLngBounds();
    bounds.extend(start);
    bounds.extend(end);
    map.fitBounds(bounds);
    var request = {
      // zoomControl: false,
      origin: start,
      destination: end,
      travelMode: google.maps.TravelMode.DRIVING,
    };
    directionsService.route(request, function(response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        directionsDisplay.setDirections(response);
        directionsDisplay.setMap(map);
      } else {
        alert("Error getting directions");
      }
    });
  }

  // google.maps.event.addDomListener(window, 'load', initialize);
  initialize();

}
// initMap();

window.addEventListener('load', initMap);
