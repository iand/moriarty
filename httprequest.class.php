<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moriarty.inc.php';
require_once MORIARTY_DIR . 'httpcache.class.php';

/**
 * Represents an HTTP protocol request.
 */
class HttpRequest {
  /**
   * @access private
   */
  var $method;
  /**
   * @access private
   */
  var $uri;
  /**
   * @access private
   */
  var $headers = array();
  /**
   * @access private
   */
  var $client;
  /**
   * @access private
   */
  var $body;
  /**
   * @access private
   */
  var $credentials;

  /**
   * Create a new instance of this class
   * @param string method the HTTP method to issue (i.e. GET, POST, PUT etc)
   * @param string uri the URI to issue the request to
   * @param Credentials credentials the credentials to use for secure requests (optional)
   */
  function __construct($method, $uri, $credentials = null) {
    $this->uri = $uri;
    $this->method = strtoupper($method);
    if ( $credentials != null ) {
      $this->credentials = $credentials;
    }
    else {
       $this->credentials = null;
    }
    
    $this->headers = array();
    $this->options = array();
    $this->body = null;

  }


  /**
   * Issue the HTTP request
   * @return HttpResponse
   */
  function execute() {

    if ( defined('MORIARTY_HTTP_CACHE_DIR') ) {
      $cache = new HttpCache(MORIARTY_HTTP_CACHE_DIR);

      $cached_response = $cache->get_cached_response($this);
      if ($cached_response) {
        
        if (defined('MORIARTY_HTTP_CACHE_READ_ONLY') && $cache->is_fresh($this, $cached_response)) {
          $cached_response->request = $this;
          return $cached_response;
        }
        else {
          if ( isset($cached_response->headers['etag']) ) {
            $this->set_if_none_match($cached_response->headers['etag']);
          }
        }
      }
    }

    if (class_exists('http_class') && class_exists('sasl_interact_class')) {
      set_time_limit(0);
      $http=new http_class;
      $http->follow_redirect=1;
      $http->redirection_limit=5;
      $http->prefer_curl=0;
      
//  $http->debug=1;
//  $http->html_debug=1;
        
      $error=$http->GetRequestArguments($this->uri,$arguments);
      if ($error) {
        echo htmlspecialchars($error);  
      }
      if ($this->credentials != null) {
        $http->authentication_mechanism="Digest";
        $arguments['AuthUser'] = $this->credentials->get_username();
        $arguments['AuthPassword'] = $this->credentials->get_password();
      }      


      $arguments["RequestMethod"]=$this->method;

      foreach ($this->headers as $k=>$v) {
        $arguments["Headers"][$k] = $v;
      }
   
      if ($this->body != null) {
        $arguments["Body"] = $this->body;
      }
      $response_info = array();
      $connect_error = '';
            
      $connect_error = $http->Open($arguments);
      if (! $connect_error) {
        $connect_error = $http->SendRequest($arguments);
      }
    
      if ( ! $connect_error ) {
        $response_headers=array();
        $error = $http->ReadReplyHeaders($response_headers);
        $response_code = $http->response_status;
        $response_body = '';

        for(;;) {
          $error=$http->ReadReplyBody($body,1000);
          if($error!="" || strlen($body)==0)
            break;
          $response_body .= $body;
        }

        if ( defined('MORIARTY_HTTP_CACHE_DIR') ) {
          if ( $cached_response && ! $cache->is_fresh($this, $cached_response) ) {
            $cache->remove_from_cache($this); 
          }
        }
      }
      else {
        if ( defined('MORIARTY_HTTP_CACHE_DIR') && $cached_response) {
          if ( defined('MORIARTY_HTTP_CACHE_USE_STALE_ON_FAILURE') ) {
            $cached_response->request = $this;
            return $cached_response;
          }
          else if ( !$cache->is_fresh($this, $cached_response) ) {
            $cache->remove_from_cache($this); // cached response was definitely stale because we check above
          }
        }

        $response_code = $response_info['http_code'];
        $response_body = "Request failed: " . $response_error;
        $response_headers = array();
      }
      
      $http->Close();
        

    }
    else {
    
      $poster = curl_init($this->uri);

      if ($this->credentials != null) {
        curl_setopt($poster, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($poster, CURLOPT_USERPWD, $this->credentials->get_auth());
      }
     // curl_setopt($poster, CURLOPT_VERBOSE, 1);


      curl_setopt($poster, CURLOPT_FRESH_CONNECT,TRUE);

      curl_setopt($poster, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($poster, CURLOPT_FOLLOWLOCATION, TRUE);
      curl_setopt($poster, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($poster, CURLOPT_TIMEOUT, 600);
      curl_setopt($poster, CURLOPT_HEADER, 1);

      if ( defined( 'MORIARTY_PROXY' ) ) {
        curl_setopt($poster, CURLOPT_PROXY, MORIARTY_PROXY );
      }

      switch($this->method) {
          case 'GET'  : break;
          case 'POST' : curl_setopt($poster, CURLOPT_POST, 1); break;
          default     : curl_setopt($poster, CURLOPT_CUSTOMREQUEST,strtoupper($this->method));
      }

      if ($this->body != null) {
        curl_setopt($poster, CURLOPT_POSTFIELDS, $this->body);
      }

      curl_setopt($poster, CURLOPT_HTTPHEADER, $this->get_headers() );


      $raw_response = curl_exec($poster);
      $response_info = curl_getinfo($poster);
      $response_error = curl_error($poster);
      curl_close($poster);

      if ( $raw_response ) {
        list($response_code,$response_headers,$response_body) = $this->parse_response($raw_response);
        if ( defined('MORIARTY_HTTP_CACHE_DIR') && $cached_response && ! $cache->is_fresh($this, $cached_response) ) {
          $cache->remove_from_cache($this); 
        }
        
      }
      else {
        if ( defined('MORIARTY_HTTP_CACHE_DIR') && $cached_response) {
          if ( defined('MORIARTY_HTTP_CACHE_USE_STALE_ON_FAILURE') ) {
            $cached_response->request = $this;
            return $cached_response;
          }
          else if ( !$cache->is_fresh($this, $cached_response) ) {
            $cache->remove_from_cache($this); // cached response was definitely stale because we check above
          }
        }

        $response_code = $response_info['http_code'];
        $response_body = "Request failed: " . $response_error;
        $response_headers = array();
      }
    }

    $response = new HttpResponse();
    $response->status_code = $response_code;
    $response->headers = $response_headers;
    $response->body = $response_body;
    $response->info = $response_info;
    $response->request = $this;


/*    
  echo '<p>The HTTP request sent was:</p>';
  echo '<pre>' . htmlspecialchars($this->to_string()) . '</pre>';
  echo '<p>The server response was:</p>';
  echo '<pre>' . htmlspecialchars($response->to_string()) . '</pre>';
*/
      
    if ( defined('MORIARTY_HTTP_CACHE_DIR') ) {
      if ( $cached_response && $response_code == 304) {
        $cached_response->request = $this;
        return $cached_response;
      }

      if ( $this->method == 'GET' && $response->is_cacheable() ) {
        $cache->write($this, $response);
      }
    }
    
    return $response;

  }

  /**
   * Obtain the HTTP headers to be sent with this request
   * @return array headers in the format "name:value"
   */
  function get_headers() {
    $flat_headers = array();
    foreach ($this->headers as $k=>$v) {
      $flat_headers[] = "$k: $v";
    }
    return $flat_headers;
  }

  /**
   * Set content to be sent with the request
   * @param string val the content to be sent
   */
  function set_body($val) {
    $this->body = $val;
  }

  /**
   * Get the content to be sent with the request
   * @return string the content to be sent
   */
  function get_body() {
    return $this->body;
  }

  /**
   * Set the HTTP accept header for the request
   * @param string val the media types to be used as the accept header value
   */
  function set_accept($val) {
    $this->headers['Accept'] = $val;
  }

  /**
   * Set the HTTP content-type header for the request
   * @param string val the media type to be used as the content-type header value
   */
  function set_content_type($val) {
    $this->headers['Content-Type'] = $val;
  }

  /**
   * Set the HTTP if-match header for the request
   * @param string val the etag to be used as the if-match header value
   */
  function set_if_match($val) {
    $this->headers['If-Match'] = $val;
  }
  
  /**
   * Set the HTTP if-none-match header for the request
   * @param string val the etag to be used as the if-none-match header value
   */
  function set_if_none_match($val) {
    $this->headers['If-None-Match'] = $val;
  }
   
  /**
   * @access private
   */
  function parse_response($response){
   /*
   ***original code extracted from examples at
   ***http://www.webreference.com/programming
                           /php/cookbook/chap11/1/3.html

   ***returns an array in the following format which varies depending on headers returned

       [0] => the HTTP error or response code such as 404
       [1] => Array
       (
           [Server] => Microsoft-IIS/5.0
           [Date] => Wed, 28 Apr 2004 23:29:20 GMT
           [X-Powered-By] => ASP.NET
           [Connection] => close
           [Set-Cookie] => COOKIESTUFF
           [Expires] => Thu, 01 Dec 1994 16:00:00 GMT
           [Content-Type] => text/html
           [Content-Length] => 4040
       )
       [2] => Response body (string)
*/

   do
     {
      if ( strstr($response, "\r\n\r\n") == FALSE) {
        $response_headers = $response;
        $response = '';
      }
      else {
       list($response_headers,$response) = explode("\r\n\r\n",$response,2);
      }
       $response_header_lines = explode("\r\n",$response_headers);

   // first line of headers is the HTTP response code
       $http_response_line = array_shift($response_header_lines);
       if (preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@',$http_response_line,
                     $matches)) {
         $response_code = $matches[1];
       }
       else
         {
           $response_code = "Error";
         }
     }
   while (preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@',$response));

   $response_body = $response;

   // put the rest of the headers in an array
   $response_header_array = array();
   foreach ($response_header_lines as $header_line) {
       list($header,$value) = explode(': ',$header_line,2);
       $response_header_array[strtolower($header)] = $value;
   }

   return array($response_code,$response_header_array,$response_body);
  }

  /**
   * Obtain a string representation of this request
   * @return string
   */
  function to_string() {
    $ret = strtoupper($this->method) . ' ' . $this->uri . "\n";
    foreach ($this->headers as $k=>$v) {
      $ret .= "$k: $v\n";
    }
    $ret .= "\n";
    $ret .= $this->get_body();

    return $ret;
  }

}

/**
 * Represents an HTTP protocol response.
 */
class HttpResponse {
  /**
   * The HTTP status code of the response
   * @var int
   */
  var $status_code;
  /**
   * The HTTP headers returned with this response. This is an associative array whose keys are the header name and values are the header values.
   * @var array
   */
  var $headers = array();
  /**
   * Additional information about this response
   * @var array
   */
  var $info = array();
  /**
   * The entity body returned with the response
   * @var string
   */
  var $body;
  /**
   * The request that was responsible for generating this response
   * @var HttpRequest
   */
  var $request;
  /** 
   * @access private
   */
  var $_is_cacheable;
  /** 
   * @access private
   */
  var $_max_age;
  
  /**
   * Create a new instance of this class
   * @param int status_code the status code of the response
   */
  function __construct($status_code = null) {
    $this->status_code = $status_code;
  }

  /**
   * Tests whether the response indicates the request was successful
   * @return boolean true if the status code is between 200 and 299 inclusive, false otherwise
   */
  function is_success() {
    return $this->status_code >= 200 && $this->status_code < 300;
  }


  /**
   * Obtain a string representation of this response
   * @return string
   */
  function to_string() {
    $ret = $this->status_code . "\n";
    foreach ($this->headers as $k=>$v) {
      $ret .= "$k: $v\n";
    }
    $ret .= "\n";
    $ret .= $this->body;

    return $ret;
  }

  /**
   * Tests whether this response is suitable for caching
   * @return boolean true if the response can be cached, false otherwise
   */
  function is_cacheable() {
    if (!isset($this->_is_cacheable)) {
      if ( isset($this->headers['cache-control'])) {
        $cache_control = $this->headers['cache-control'];
        $cache_control_tokens = split(',', $cache_control);
        foreach ( $cache_control_tokens as $token) {
          $token = trim($token);
          if ( preg_match('/private/', $token, $m) ) {
            $this->_is_cacheable = false;
            return false;
          }
          elseif ( preg_match('/no-cache/', $token, $m) ) {
            $this->_is_cacheable = false;
            return false;
          }
          elseif ( preg_match('/no-store/', $token, $m) ) {
            $this->_is_cacheable = false;
            return false;
          }
        }
      }


      if ( $this->status_code == 200 ||
           $this->status_code == 203 ||
           $this->status_code == 300 ||
           $this->status_code == 301 ||
           $this->status_code == 410 ) {
        $this->_is_cacheable = true;
      }
      else {
        $this->_is_cacheable = false;
      }            
    }
    return $this->_is_cacheable;
  }


}

?>
