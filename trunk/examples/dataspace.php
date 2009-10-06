<?php
// CHANGE THESE TO POINT TO YOUR INSTALLATIONS OF MORIARTY AND ARC
define('MORIARTY_DIR', '../');
define('MORIARTY_ARC_DIR', '../../../web/lib/arc_2008_11_18/');

// CHANGE THIS TO POINT TO YOUR STORE
define('STORE_URI', 'http://api.talis.com/stores/iand');

$uri = 'http://iandavis.com/id/me';
if (array_key_exists('uri', $_GET)) {
  $uri = stripslashes($_GET['uri']);
}

?>
<html>
  <body>
    <form action="" method="get">
      <label for="uri">URI:</label> <input name="uri" id="uri" type="text" size="40" value="<?php echo(htmlspecialchars($uri)); ?>"/> <input type="submit" />
    </form>

<?php
if ($uri) {
  require_once MORIARTY_DIR . 'moriarty.inc.php';
  require_once MORIARTY_DIR . 'store.class.php';
  require_once MORIARTY_DIR . 'simplegraph.class.php';



  $store = new Store(STORE_URI);
  $response = $store->describe($uri, 'lcbd', 'json');
  if ($response->is_success()) {
    $g = new SimpleGraph();
    $g->from_json($response->body);
    echo $g->to_html($uri);
  }
  else {
    echo '<p>Error: ' . htmlspecialchars($response->to_string()) . '</p>';
  }
}
?>
  </body>
</html>
