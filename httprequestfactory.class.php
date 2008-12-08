<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moriarty.inc.php';
require_once MORIARTY_DIR. 'httprequest.class.php';

/**
 * A factory for creating instances of HttpRequests. Required so unit tests can mock out HTTP behaviour
 */
class HttpRequestFactory {
  function make( $method, $uri, $credentials = null) {
    return new HttpRequest( $method, $uri, $credentials );
  }
}
?>
