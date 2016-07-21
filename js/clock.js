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
