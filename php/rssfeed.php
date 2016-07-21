<?php

$news = array();
$url = "http://rss.nytimes.com/services/xml/rss/nyt/Technology.xml";
$xml = simplexml_load_file($url);
for ($i=0; $i<10; $i++) {
  $title = $xml->channel->item[$i]->title;
  array_push($news, $title);
}

?>
