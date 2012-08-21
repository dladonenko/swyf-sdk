swyf-sdk
========

The official Swyf SDK for using faceted lookbook and product search. You can use this API when you implemented the lookbook application into your website.

## Swyf API
The API is built around the REST principles:

-   HTTP standards are used wherever possible.
-   All requests are stateless.
-   The URIs are directory like.
-   Output type can be either json or xml.

### HTTP methods
The following HTTP request methods are supported by the API, based on your account type:

-   __`GET /resource`__  
    Gives a list of objects of the resource type specified. Optionally some query parameters can be used in order to apply filtering.
-   __`GET /resource/:resource_id`__  
    Gives the object based on it's resource type and id.
-   __`POST /resource`__  
    Creates a new object of the given resource type. When the object has been created successfully a 201 Status will be returned with the created object in the Location header.
-   __`PUT /resource/:resource_id`__  
    __`POST /resource/:resource_id?_method=PUT`__  
    Updates an object based on the resource type and the given id.
-   __`DELETE /resource/:resource_id`__  
    __`POST /resource/:resource_id?_method=DELETE`__  
    Marks an object as deleted based on the resource type and the given id.

### Formatting
The API supports both JSON (default, preferred) and XML. 

When retrieving information from the API there are two ways in which the format can be specified. When both ways are used, the query parameter has the highest precedence.

1.  __`Accept` HTTP header__  
    This can be passed on by each request. The values can be either `application/json` or `application/xml`. The SDK automatically and only supports JSON.
2.  __`_format` query parameter__  
    This parameter can always be appended to the API url. The possible values are: `json` or `xml`.
    
When updating or creating objects the `Content-Type` HTTP header should be used. The possible values are - of course - equal to the `Accept` header.

### Authentication
Of course all requests to the API require authentication. This is implemented by means of an application with a secret. The information needs to be supplied with each request by adding the `X-API-Application` and `X-API-Secret` http headers. This is of course implemented in the Swyf SDK.

### Resource types
A few resources can be retrieved from the API, some of them can even be filtered:

1. __product__  
2. __lookbook__  
3. __shop__  
4. __user-account__

### API routes
The resource types as described above can be used together in order to implement filtering for the endpoint. An endpoint is defined as the last part of the url. Consider the API url: `https://api.shopwithyourfriends.com/api/v1/lookbook/8734/product`. It consists of the following parts:

-   `https://`: the protocol is always SSL;
-   `api.shopwithyourfriends.com`: the hostname is always fixed;
-   `/api/v1`: fixed prefix for versioning;
-   `/lookbook/8743`: filter this request based on this specific lookbook;
-   `/product`: the specific API endpoint.

The following routes and filters are defined in the system:

-   `/product`
-   `/product/:product_id`
-   `/product/:product_id/shop`
-   `/lookbook`
-   `/lookbook/:lookbook_id`
-   `/lookbook/:lookbook_id/product`
-   `/lookbook/:lookbook_id/user-account`
-   `/shop`
-   `/shop/:shop_id`

### Errors
There are two types of errors the API can return: Auth and Request. All errors in the platform have a three digit code, a message and an HTTP response code.

#### Auth errors
<table>
	<tr>
		<th>error code</th><th>http status</th><th>error message</th>
	</tr>
	<tr>
		<td>100</td><td>401</td><td>Authentication failed</td>
	</tr>
	<tr>
		<td>101</td><td>401</td><td>Invalid credentials</td>
	</tr>
	<tr>
		<td>102</td><td>403</td><td>Not allowed to %s on %s.</td>
	</tr>
	<tr>
		<td>103</td><td>403</td><td>Not allowed to access object %s with id %s.</td>
	</tr>
</table>

#### Request errors
<table>
	<tr>
		<th>error code</th><th>http status</th><th>error message</th>
	</tr>
	<tr>
		<td>200</td><td>500</td><td>Request failed</td>
	</tr>
	<tr>
		<td>201</td><td>501</td><td>Unknown object requested: %s.</td>
	</tr>
	<tr>
		<td>202</td><td>404</td><td>Object %s with id %s doesn't exist.</td>
	</tr>
	<tr>
		<td>203</td><td>501</td><td>The requested object is invalid.</td>
	</tr>
	<tr>
		<td>204</td><td>405</td><td>The requested method is invalid: %s.</td>
	</tr>
	<tr>
		<td>205</td><td>400</td><td>Method %s needs an object id, which has not been specified.</td>
	</tr>
	<tr>
		<td>206</td><td>400</td><td>Method %s does not need an object id, but has been specified: %s.</td>
	</tr>
	<tr>
		<td>207</td><td>400</td><td>Some of the parameters are missing: %s.</td>
	</tr>
	<tr>
		<td>208</td><td>500</td><td>Failed to insert data: %s.</td>
	</tr>
	<tr>
		<td>209</td><td>500</td><td>Failed to update data: %s.</td>
	</tr>
	<tr>
		<td>210</td><td>500</td><td>Failed to delete data: %s.</td>
	</tr>
	<tr>
		<td>211</td><td>404</td><td>Object %s with id %s is not active.</td>
	</tr>
	<tr>
		<td>212</td><td>400</td><td>Parameter '%s' with value '%s' is invalid.</td>
	</tr>
	<tr>
		<td>213</td><td>400</td><td>SnUser with social network ID %s in %s already exists.</td>
	</tr>
	<tr>
		<td>214</td><td>400</td><td>There are more parameters found than required.</td>
	</tr>
	<tr>
		<td>215</td><td>400</td><td>Method %s on object %s can only be called when filtered through: %s.</td>
	</tr>
	<tr>
		<td>216</td><td>400</td><td>Method %s on object %s is not implemented.</td>
	</tr>
	<tr>
		<td>217</td><td>403</td><td>UserAccountSetting %s for user %s already exists.</td>
	</tr>
</table>

## Swyf PHP SDK
The Swyf PHP SDK is a very small wrapper which handles all communication, encoding, authentication and url handling which is provided by the Swyf API. The library is open sourced and everyone is encouraged to file bugs, complete documentation etc.

The getting started for the SDK is fairly short:

1. Obtain an application and secret from the team.
2. Make sure you either have a [psr-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) based autoloader OR have [composer](http://getcomposer.org) installed.
3. Initialize a `new Swyf_Api($application, $secret)` object.
4. Perform an API request with `get`, `post`, `put`, `delete`.

### Exceptions
Three types of exceptions can be thrown from all API requests. The types are:

-   __Swyf_Api_Exception__  
    Exception used for API internal errors as described in the API documentation. The codes and messages match.
-   __Swyf_Http_Exception__  
    For all problems in the construction of the API request.
-   __Swyf_Http_Exception_Curl__  
    For all errors returned by the cURL module, which can be both system and request errors.


# To be completed

- Explanation of resource types.
- Properties of a resource type in list and detail.
- Curl properties for each resource type list/get
- Filter parameters for product search.
- Filter parameters for lookbook search.
Outputs.
