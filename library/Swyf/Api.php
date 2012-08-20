<?php
/**
 * Swyf API object.
 * 
 * Constructs and executes queries on the API of Swyf.
 * 
 * @package Swyf
 * @subpackage Api
 * @author Jasper van Wanrooy <jvanwanrooy@swyf.com>
 */
class Swyf_Api
{
	/**
	 * Holds the application id for all requests.
	 * 
	 * @var string
	 */
	protected $_application;
	
	/**
	 * Holds the secret for all requests.
	 * 
	 * @var string
	 */
	protected $_secret;
	
	/**
	 * Holds the user agent for all requests.
	 * 
	 * @var string
	 */
	protected $_user_agent = 'swyf_api/1.0';
	
	/**
	 * Holds the base url for all requests.
	 * 
	 * @var string
	 */
	protected $_base_url = 'https://api.shopwithyourfriends.com/api/v1/';
	
	/**
	 * Holds the content type for the requests. 
	 * 
	 * @var string
	 */
	protected $_content_type = 'application/json';
	
	/**
	 * Constructs a SWYF API object.
	 * 
	 * @param string $application The application name as supplied by the Swyf team.
	 * @param string $secret      The API secret as supplied by the Swyf team.
	 */
	public function __construct($application, $secret)
	{
		$this->_application = $application;
		$this->_secret      = $secret;
	}
	
	/**
	 * Gets the application id for all requests.
	 * 
	 * @return string
	 */
	public function getApplication()
	{
		return $this->_application;
	}
	
	/**
	 * Gets the secret for all requests.
	 * 
	 * @return string
	 */
	public function getSecret()
	{
		return $this->_secret;
	}
	
	/**
	 * Gets the user agent for all requests.
	 * 
	 * @return string
	 */
	public function getUserAgent()
	{
		return $this->_user_agent;
	}
	
	/**
	 * Set the user agent for the API requests.
	 * 
	 * @param string $user_agent
	 * @return Swyf_Api
	 */
	public function setUserAgent($user_agent)
	{
		$this->_user_agent = $user_agent;
		return $this;
	}
	
	/**
	 * Gets the base url for all api calls.
	 * 
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->_base_url;
	}
	
	/**
	 * Set the base url for the API requests.
	 * 
	 * @param string $base_url
	 * @return Swyf_Api
	 */
	public function setBaseUrl($base_url)
	{
		if (!preg_match('/\/$/', $base_url))
		{
			$base_url .= '/';
		}
		
		$this->_base_url = $base_url;
		return $this;
	}
	
	/**
	 * Performs a GET call to the API path. Optionally some extra GET params can be added.
	 * 
	 * @param string $url
	 * @param array $get_params
	 * @throws Swyf_Api_Exception When the API returned an error.
	 * @return stdClass
	 */
	public function get($url, $get_params = array())
	{
		try
		{
			$request = new Swyf_Http_Request(
				$this->_getRequestUrl($url),
				'GET',
				$this->getUserAgent()
			);
			$this->_setDefaultHeaders($request);
			
			$response = $request->performRequest($get_params);
			return $this->_handleResponse($response);
		}
		catch (Swyf_Http_Exception_Curl $e)
		{
			return $this->_handleCurlException($e);
		}
	}
	
	/**
	 * Creates a new object. Automatically fetches the new Location when the object has been created
	 * successfully.
	 * 
	 * @param string $url
	 * @param array $values
	 * @throws Swyf_Api_Exception When the API returned an error.
	 * @return stdClass
	 */
	public function post($url, $values = array())
	{
		try
		{
			$request = new Swyf_Http_Request(
				$this->_getRequestUrl($url),
				'POST',
				$this->getUserAgent()
			);
			$this->_setDefaultHeaders($request);
			
			$response = $request->performRequest(array(), json_encode($values));
		}
		catch (Swyf_Http_Exception_Curl $e)
		{
			return $this->_handleCurlException($e);
		}
		
		/**
		 * When the HTTP code equals 201, the object was created. Immediately perform a GET to get
		 * the object.
		 */
		if ($response->getStatusCode() == 201)
		{
			return $this->get($response->getHeader('Location'));
		}
		else
		{
			return $this->_handleResponse($response);
		}
	}
	
	/**
	 * Updates an object.
	 * 
	 * @param string $url
	 * @param array  $values
	 * @throws Swyf_Api_Exception When the API returned an error.
	 * @return stdClass
	 */
	public function put($url, $values = array())
	{
		try
		{
			$request = new Swyf_Http_Request(
				$this->_getRequestUrl($url),
				'PUT',
				$this->getUserAgent()
			);
			$this->_setDefaultHeaders($request);
			
			$response = $request->performRequest(array(), json_encode($values));
			return $this->_handleResponse($response);
		}
		catch (Swyf_Http_Exception_Curl $e)
		{
			return $this->_handleCurlException($e);
		}
	}
	
	/**
	 * Deletes an object.
	 * 
	 * @param string $url
	 * @throws Swyf_Api_Exception When the API returned an error.
	 * @return stdClass
	 */
	public function delete($url)
	{
		try
		{
			$request = new Swyf_Http_Request(
				$this->_getRequestUrl($url),
				'DELETE',
				$this->getUserAgent()
			);
			$this->_setDefaultHeaders($request);
			
			$response = $request->performRequest();
			return $this->_handleResponse($response);
		}
		catch (Swyf_Http_Exception_Curl $e)
		{
			return $this->_handleCurlException($e);
		}
	}
	
	/**
	 * Sets the default http headers for API communication.
	 * 
	 * @param Swyf_Http_Request $request
	 * @return Swyf_Http_Request
	 */
	private function _setDefaultHeaders(Swyf_Http_Request $request)
	{
		$request->setHttpHeader('X-API-Application', $this->getApplication());
		$request->setHttpHeader('X-API-Secret', $this->getSecret());
		$request->setHttpHeader('Content-Type', $this->_content_type);
		return $request;
	}
	
	/**
	 * Handles an API response.
	 * 
	 * @param Swyf_Http_Response $response
	 * @throws Swyf_Api_Exception When the API returned an error.
	 * @return stdClass
	 */
	private function _handleResponse(Swyf_Http_Response $response)
	{
		$response_decoded = json_decode($response->getBody());
		
		/**
		 * When the response is an error, make an exception of it.
		 */
		if (isset($response_decoded->error))
		{
			throw new Swyf_Api_Exception(
				$response_decoded->error->type . ' error: ' . $response_decoded->error->message,
				$response_decoded->error->code
			);
		}
		
		/**
		 * Response ok. Return the JSON decoded object.
		 */
		return $response_decoded;
	}
	
	/**
	 * Handles the exception for a Curl error.
	 * 
	 * @param Swyf_Http_Exception_Curl $e
	 * @throws Swyf_Api_Exception
	 */
	private function _handleCurlException(Swyf_Http_Exception_Curl $e)
	{
		throw new Swyf_Api_Exception(
			"CURL ran into an error. Message: '" . $e->getMessage() .
			"'. Code: '" . $e->getCode() . "'"
		);
	}
	
	/**
	 * Prepares the url for use. Accepts either a path, which needs to be prefixed with the base
	 * url, or checks for a valid url.
	 * 
	 * @param string $url
	 * @return string
	 */
	private function _getRequestUrl($url)
	{
		if (substr($url, 0, strlen($this->getBaseUrl())) === $this->getBaseUrl())
		{
			return $url;
		}
		else if (substr($url, 0, 7) !== 'http://' || substr($url, 0, 8) !== 'https://')
		{
			return $this->getBaseUrl() . $url;
		}
		else
		{
			throw new Swyf_Api_Exception('Invalid forward URL for request: ' . $url);
		}
	}
}
