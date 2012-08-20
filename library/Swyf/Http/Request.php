<?php
/**
 * Swyf HTTP request object.
 * 
 * Creates plain http requests to a specified url.
 * 
 * @package Swyf
 * @subpackage Http
 * @author Jasper van Wanrooy <jvanwanrooy@swyf.com>
 */
class Swyf_Http_Request
{
	/**
	 * Holds the uri for the request.
	 * 
	 * @var string
	 */
	protected $_uri;
	
	/**
	 * Holds the HTTP method type.
	 * 
	 * @var string
	 */
	protected $_method;
	
	/**
	 * Holds the user agent for the request.
	 * 
	 * @var string
	 */
	protected $_user_agent;
	
	/**
	 * Holds the HTTP headers.
	 * 
	 * @var array
	 */
	protected $_headers = array();
	
	/**
	 * Holds optional curl options.
	 * 
	 * @var array
	 */
	protected $_curl_opt = array();
	
	/**
	 * Construct an HTTP request object.
	 * 
	 * @param string $uri
	 * @param string $method
	 * @param string $useragent
	 */
	public function __construct($uri, $method, $useragent = '')
	{
		$this->_uri        = $uri;
		$this->_method     = $method;
		$this->_user_agent = $useragent;
	}
	
	/**
	 * Set a Curl opt (only the part after CURLOPT_).
	 * 
	 * @param string $opt_const
	 * @param mixed $value
	 */
	public function setCurlOpt($opt_const, $value)
	{
		$const_value = constant('CURLOPT_' . strtoupper($opt_const));
		if ($const_value)
		{
			$this->_curl_opt[$const_value] = $value;
		}
	}
	
	/**
	 * Add a custom header to the request.
	 * 
	 * @param string $name
	 * @param string $value
	 * @return Swyf_Http_Request
	 */
	public function setHttpHeader($name, $value)
	{
		$this->_headers[] = "{$name}: {$value}";
		return $this;
	}
	
	/**
	 * Performs the request using curl.
	 * 
	 * @param array $get_vars with key-value pairs.
	 * @param mixed $postdata This can be an associative array with multiple params or a string
	 * @throws Swyf_Http_Exception When invalid input has been passed on.
	 * @throws Swyf_Http_Exception_Curl When a curl error has been occurred.
	 * @return Swyf_Http_Response
	 */
	public function performRequest($get_vars = array(), $postdata = '')
	{
		/**
		 * Initiate request
		 */
		$curl_handle = curl_init();
		$start_time  = microtime(true);
		
		/**
		 * Set default options
		 */
		curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		
		// nasty hack to work around a proper ssl certificate.
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		
		/**
		 * Calculate the uri.
		 */
		if (!empty($get_vars))
		{
			$this->_uri .= (parse_url($this->_uri, PHP_URL_QUERY)) ? '&' : '?';
			$this->_uri .= http_build_query($get_vars, '', '&');
		}
		curl_setopt($curl_handle, CURLOPT_URL, $this->_uri);
		
		/**
		 * Only add user agent when it is supplied.
		 */
		if (!empty($this->_user_agent))
		{
			curl_setopt($curl_handle, CURLOPT_USERAGENT, $this->_user_agent);
		}
		
		/**
		 * Set the request method and request specific options.
		 */
		if ($this->_method == 'GET')
		{
			curl_setopt($curl_handle, CURLOPT_HTTPGET, true);
		}
		else if ($this->_method == 'POST' || $this->_method == 'PUT')
		{
			/**
			 * Build up the data to submit.
			 */
			if (is_array($postdata))
			{
				$postdata = http_build_query($postdata, '', '&');
			}
			else if (!is_string($postdata))
			{
				throw new Swyf_Http_Exception('Invalid post data specified.');
			}
			
			/**
			 * Setup the fields for POST.
			 */
			if ($this->_method == 'POST')
			{
				curl_setopt($curl_handle, CURLOPT_POST, true);
			}
			
			elseif ($this->_method == 'PUT')
			{
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT');
				$this->setHttpHeader('Content-Length', mb_strlen($postdata));
			}
			
			/**
			 * Set the data to be posted.
			 */
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postdata);
		}
		else if ($this->_method == 'DELETE')
		{
			curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}
		else
		{
			throw new Swyf_Http_Exception('Invalid HTTP method specified. Only GET, POST, PUT and DELETE supported at the moment.');
		}
		
		/**
		 * Add the custom headers to the request.
		 */
		if(!empty($this->_headers))
		{
			curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $this->_headers);
		}
		
		/**
		 * Initiate the response processor and set the header handler.
		 */
		$response_processor = new Swyf_Http_Response();
		curl_setopt($curl_handle, CURLOPT_HEADERFUNCTION, array($response_processor, 'setHeader'));
		curl_setopt($curl_handle, CURLOPT_HEADER, false);
		
		/**
		 * Set the individual curl opts.
		 */
		foreach ($this->_curl_opt as $_key => $_value)
		{
			curl_setopt($curl_handle, $_key, $_value);
		}
		
		/**
		 * Handle the request.
		 */
		$response   = curl_exec($curl_handle);
		$stop_time  = microtime(true);
		$curl_errno = (int) curl_errno($curl_handle);
		$curl_error = curl_error($curl_handle); 
		
		if (!$curl_errno)
		{
			$duration = $stop_time - $start_time;
			
			/**
			 * Set the responses
			 */
			$response_processor->setResponse($response);
			$response_processor->setDuration($duration);
		}
		else
		{
			/**
			 * The curl error numbers can be found here: http://curl.haxx.se/libcurl/c/libcurl-errors.html
			 */
			throw new Swyf_Http_Exception_Curl($curl_error, $curl_errno);
		}
		
		/**
		 * Finalize it.
		 */
		curl_close($curl_handle);
		return $response_processor;
	}
}
