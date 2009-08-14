<?php

class CurlHttpClient extends HttpClient
{
	private $curl_handles = array();
	private $responses = array();
	private $multicurl;
	private $running;

	public function __construct()
	{
		$this->multicurl = curl_multi_init();
	}

	public function send_request($request)
	{
		$curl_handle = curl_init($request->uri);
		$key = (string)$curl_handle;
		$this->curl_handles[$key] = $curl_handle;

		if ($request->credentials != null) {
			curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt($curl_handle, CURLOPT_USERPWD, $request->credentials->get_auth());
		}
		// curl_setopt($curl_handle, CURLOPT_VERBOSE, 1);

		curl_setopt($curl_handle, CURLOPT_FRESH_CONNECT,TRUE);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($curl_handle, CURLOPT_TIMEOUT, 600);
		curl_setopt($curl_handle, CURLOPT_HEADER, 1);

		if ( !empty( $request->_proxy ) ) {
			curl_setopt($curl_handle, CURLOPT_PROXY, $request->_proxy );
		}

		switch($request->method) {
			case 'GET'  : break;
			case 'POST' : curl_setopt($curl_handle, CURLOPT_POST, 1); break;
			default     : curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST,strtoupper($this->method));
		}

		if ($request->body != null) {
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $request->body);
		}

		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $request->get_headers() );

		$res = curl_multi_add_handle($this->multicurl, $curl_handle);

		if($res === CURLM_OK || $res === CURLM_CALL_MULTI_PERFORM)
		{
			do
			{
				$this->execStatus = curl_multi_exec($this->multicurl, $this->running);
			}
			while ($this->execStatus === CURLM_CALL_MULTI_PERFORM);
		}

		return $key;
	}

	public function get_response_for($key)
	{
		if(isset($this->responses[$key]))
		{
			return $this->responses[$key];
		}

		$innerSleepInt = $outerSleepInt = 1;
		$sleepIncrement = 1.1;

		$status = CURLM_OK;
		while($this->running && ($this->execStatus == CURLM_OK || $this->execStatus == CURLM_CALL_MULTI_PERFORM))
		{
			usleep($outerSleepInt);
			$outerSleepInt *= $sleepIncrement;
			$ms = curl_multi_select($this->multicurl, 0);
			if($ms > 0)
			{
				do
				{
					$this->execStatus = curl_multi_exec($this->multicurl, $this->running);
					usleep($innerSleepInt);
					$innerSleepInt *= $sleepIncrement;
					usleep($innerSleepInt);
				}
				while($this->execStatus == CURLM_CALL_MULTI_PERFORM);

				$innerSleepInt = 0;
			}

			$this->storeResponses();

			if(isset($this->responses[$key]))
			{
				return $this->responses[$key];
			}

			$this->runningCurrent = $this->running;
		}
		return null;
	}

	private function storeResponses()
	{
		while($done = curl_multi_info_read($this->multicurl))
		{
			$key = (string)$done['handle'];
			$this->responses[$key] = curl_multi_getcontent($done['handle']);
		}
	}
}


?>