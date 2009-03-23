<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moriarty.inc.php';
require_once MORIARTY_DIR. 'httprequest.class.php';

/**
 * A factory for creating instances of HttpRequests. Required so unit tests can mock out HTTP behaviour
 */
class HttpRequestFactory {
  var $_cache = null;
  
  function make( $method, $uri, $credentials = null) {
    $request = new HttpRequest( $method, $uri, $credentials );

    if ( ! $this->_cache && defined('MORIARTY_HTTP_CACHE_DIR') ) {
      $this->_cache = new HttpCache( array('directory' => MORIARTY_HTTP_CACHE_DIR) );
    }
    
    $request->set_cache($this->_cache);
    
    return $request;
  }
  
  function set_cache($cache) {
    $this->_cache = $cache; 
  }
  
}
?>
