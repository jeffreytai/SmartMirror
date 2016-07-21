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
