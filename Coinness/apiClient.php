<?php
namespace Coinness;

const COINNESS_LANGUAGE_CN = 1;
const COINNESS_LANGUAGE_EN = 3;
const COINNESS_LANGUAGE_KO = 4;

class apiClient{

// 	CURL requests the relevant parameters
	public $useragent = 'Coinness PHPSDK v1.x';
	public $connecttimeout = 2;
	public $timeout = 30;
	public $ssl_verifypeer = FALSE;
	
// 	CURL requests state-related data
	public $http_header = array();
	public $http_code;
	public $http_info;
	public $curl_error;
	public $curl_response;
	public $url;
	
	protected $app_id,$app_sercet;
	
	public static $base_url = 'http://api.coinness.com/';
	public function __construct($app_id,$app_sercet){
		$this->app_id = $app_id;
		$this->app_sercet = $app_sercet;
	}
	/**
	 * Get Newsflash List 
	 * 
	 * @param int $language COINNESS_LANGUAGE_*
	 * @param number $start_time 
	 * @param number $end_time 
	 * @param number $size count of newsflash, max is 500
	 * @return
	 */
	public function getNewsflashList($language,$start_time=0,$end_time=0,$size=150){
		$params = array('language'=>$language,'start_time'=>$start_time,'end_time'=>$end_time,'size'=>$size);
		
		return $this->get('newsflash/list', $params);
	}
	/**
	 * Gets the last 8 hours of messages that have been modified or deleted
	 * 
	 * @param int $language COINNESS_LANGUAGE_*
	 * @return mixed
	 */
	public function getNewsflashUpdated($language){
		$params = array('language'=>$language);
		
		return $this->get('newsflash/update_list', $params);
	}
	
	protected function get($method,$params){
		$params = $this->buildRequest($params);
		$url = self::$base_url.$method.'?'.http_build_query($params);
		
		$data = $this->http($url, 'GET');
		if($this->http_info['http_code'] == 405)
			throw new coinnessException('This interface does not support GET method requests',1003);
		return $data;
	}
	
	protected function post($method,$params){
		$request = $this->buildRequest($params);
		$url = self::$base_url.$method;
		
		$data = $this->http($url, 'POST',http_build_query($request));
		if($this->http_info['http_code'] == 405)
			throw new coinnessException('This interface does not support POST method requests',1004);
		return $data;
	}
	protected function buildRequest(array $params){
		$params['app_id'] = $this->app_id;
		$params['timestamp'] = time();
		ksort($params);
		echo http_build_query($params).$this->app_sercet;
		$params['sign'] = md5(http_build_query($params).$this->app_sercet);
		
		return $params;
	}
	/**
	 *
	 * @param string $url
	 * @param string $method
	 * @param string $postfields
	 * @return mixed
	 */
	protected function http($url, $method, $postfields = NULL) {
		$this->http_info = array();
		$ci = curl_init();
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);
		curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
	
		$method = strtoupper($method);
		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields))
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
					break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($postfields))
					$url = "{$url}?{$postfields}";
		}
		curl_setopt($ci, CURLOPT_URL, $url);
		$response = curl_exec($ci);
		$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
		$this->url = $url;
		if($response == false)
			$this->curl_error = curl_error($ci);
		$this->curl_response = $response;
		curl_close ($ci);
		
		$response = json_decode($response,true);
		if (@$response['code'])
			throw new coinnessException($response['message'],$response['code']);
		
		return $response;
	}

	/**
	 * Get the header info to store.
	 */
	public function getHeader($ch, $header) {
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}
}

class coinnessException extends \Exception{

}
