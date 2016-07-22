function displayNews() {
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
}

window.addEventListener('load', displayNews);
