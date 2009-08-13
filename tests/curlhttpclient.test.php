<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'curlhttpclient.class.php';
require_once MORIARTY_DIR . 'httprequest.class.php';
require_once MORIARTY_TEST_DIR . 'fakecredentials.class.php';
require_once MORIARTY_TEST_DIR . 'fakecache.class.php';
require_once MORIARTY_TEST_DIR . 'fakehttprequest.class.php';

class CurlHttpClientTest extends PHPUnit_Framework_TestCase {
	
	function test_send_request_and_get_response()
	{
		$client = new CurlHttpClient();
		$request = new HttpRequest('GET', 'http://www.google.com/');
		$key = $client->send_request($request);
		$response = $client->get_response_for($key);
		$this->assertContains('Server: gws', $response);
	}
	
	function test_send_request_and_get_response_concurrently()
	{
		$client = new CurlHttpClient();
		$request1 = new HttpRequest('GET', 'http://www.google.com/');
		$request2 = new HttpRequest('GET', 'http://www.yahoo.com/');

		$key1 = $client->send_request($request1);
		$key2 = $client->send_request($request2);

		$response2 = $client->get_response_for($key2);
		$response1 = $client->get_response_for($key1);
		
		$this->assertContains('Server: gws', $response1);
		$this->assertContains('X-XRDS-Location: http://open.login.yahooapis.com/openid20/www.yahoo.com/xrds', $response2);
	}
	
}

?>