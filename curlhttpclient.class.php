<?php

class CurlHttpClient extends HttpClient
{
	private $responses = array();

	public function sendRequest($request)
	{
		$poster = curl_init($request->uri);

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

		if ( !empty( $request->_proxy ) ) {
			curl_setopt($poster, CURLOPT_PROXY, $request->_proxy );
		}

		switch($request->method) {
			case 'GET'  : break;
			case 'POST' : curl_setopt($poster, CURLOPT_POST, 1); break;
			default     : curl_setopt($poster, CURLOPT_CUSTOMREQUEST,strtoupper($this->method));
		}

		if ($request->body != null) {
			curl_setopt($poster, CURLOPT_POSTFIELDS, $request->body);
		}

		curl_setopt($poster, CURLOPT_HTTPHEADER, $request->get_headers() );

		$raw_response = curl_exec($poster);
		$response_info = curl_getinfo($poster);
		$response_error = curl_error($poster);
		curl_close($poster);

		if ( $raw_response ) {
			list($response_code,$response_headers,$response_body) = $request->parse_response($raw_response);
		}
		else {
			if ( $request->_cache && $cached_response) {
				return $cached_response;
			}

			$response_code = $response_info['http_code'];
			$response_body = "Request failed: " . $response_error;
			$response_headers = array();
		}

		$response = new HttpResponse();
		$response->status_code = $response_code;
		$response->headers = $response_headers;
		$response->body = $response_body;
		$response->info = $response_info;
		$response->request = $request;

		$this->responses[$request] = $response;

	}

	public function getResponseFor($request)
	{
		return @$this->responses[$request];
	}
}


?>