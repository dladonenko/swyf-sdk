<?php
/**
 * Swyf HTTP response object.
 * 
 * @package Swyf
 * @subpackage Http
 * @author Jasper van Wanrooy <jvanwanrooy@swyf.com>
 */
class Swyf_Http_Response
{
	/**
	 * Associative array with the headers of the reponse.
	 * 
	 * @var array
	 */
	protected $_response_headers = array();
	
	/**
	 * Holds the full response body without the headers
	 * 
	 * @var string
	 */
	protected $_response_body;
	
	/**
	 * Holds the duration of the request
	 * 
	 * @var float
	 */
	protected $_duration;
	
	/**
	 * Holds the HTTP version.
	 * 
	 * @var string
	 */
	protected $_http_version;
	
	/**
	 * Holds the HTTP status response code.
	 * 
	 * @var int
	 */
	protected $_status_code;
	
	/**
	 * Holds the name of the status as returned by the server.
	 * 
	 * @var string
	 */
	protected $_status_name;
	
	/**
	 * Sets the Curl reponse object.
	 * 
	 * @param string $response
	 */
	public function setResponse($response)
	{
		$this->_response_body = $response;
	}
	
	/**
	 * Sets the duration of the request in seconds.
	 * 
	 * @param float $duration
	 */
	public function setDuration($duration)
	{
		$this->_duration = $duration;
	}
	
	/**
	 * Curl callback for setting the header info.
	 * 
	 * @param resource $handle
	 * @param string $header
	 * @return int
	 */
	public function setHeader($handle, $header)
	{
		/**
		 * Perform some basic transformations and tests 
		 */
		$byte_count = strlen($header);
		$header = trim($header);
		if (empty($header))
		{
			return $byte_count;
		}
		
		/**
		 * Determine whether we have the first HTTP header or the key - value ones.
		 */
		if (strtolower(substr($header, 0, 4)) === 'http')
		{
			preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $header, $matches);
			$this->_http_version = $matches[1];
			$this->_status_code = intval($matches[2]);
			$this->_status_name = $matches[3];
		}
		else
		{
			preg_match('#(.*?)\:(.*)#', $header, $matches);
			$this->_response_headers[$matches[1]] = isset($matches[2]) ? trim($matches[2]) : '';
		}
		
		/**
		 * @todo make this multi byte char safe.
		 */
		return $byte_count;
	}
	
	/**
	 * Gets an http header from the response.
	 * 
	 * @param string $header
	 * @return string
	 */
	public function getHeader($header)
	{
		if (isset($this->_response_headers[$header]))
		{
			return $this->_response_headers[$header];
		}
	}
	
	/**
	 * Determines whether a 2xx status code was returned.
	 * 
	 * @return bool
	 */
	public function isValid()
	{
		return (intval(substr($this->getStatusCode(), 0, 1)) === 2);
	}
	
	/**
	 * Returns the main body of the request.
	 * 
	 * @return string
	 */
	public function getBody()
	{
		return $this->_response_body;
	}
	
	/**
	 * Returns the HTTP status of the response.
	 * 
	 * @return string
	 */
	public function getHttpVersion()
	{
		return $this->_http_version;
	}
	
	/**
	 * Returns the status code of the response.
	 * 
	 * @return int
	 */
	public function getStatusCode()
	{
		return $this->_status_code;
	}
	
	/**
	 * Returns the status name of the reponse.
	 * 
	 * @return string
	 */
	public function getStatusName()
	{
		return $this->_status_name;
	}
	
	/**
	 * Returns the duration of the request.
	 * 
	 * @param bool $use_msec
	 * @param int $precision
	 * @return float 
	 */
	public function getDuration($use_msec = true, $precision = 3)
	{
		$ret = $this->_duration;
		
		if ($use_msec)
		{
			$ret = $ret * 1000;
		}
		
		return round($ret, $precision);
	}
}
