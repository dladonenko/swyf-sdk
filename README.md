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
Returns a list of multiple products with the following keys available:
	* `product_id`: identifier
	* `name`: the name of the product
	* `api_url`: url to retrieve more data about this product
	* `image_small`: url to the small image
	* `image_medium`: url to the medium image
	* `image_large`: url to the large image
	* `deeplink_buy_url`: url used to buy a product
	* `price_formatted`: pre-formatted price
	* `from_price_formatted`: pre-formatted from price (in case of a sale)
	* `shop_currency`: ISO currency used by the shop
	
	The following filters are available.
	* `limit`: limits the query (default is 10)
	* `offset`: gives the query an offset (default is 0)
	* `query`: search query (string only)
	* `category`: can be a comma separated string or an array
	* `shop`: can be a comma separated string or an array
	* `brand`: can be a comma separated string or an array
	* `price`: can be a comma separated string or an array
	* `discount`: can be a comma separated string or an array
	* `sort`: can have these values: `relevance`, `price-asc`, `price-desc`, `popularity`, `date`
		Note that date is default, with the `query` filter relevance is default.

-   `/product/:product_id`
Returns one single product with the following keys available:
	* `product_id`: identifier
	* `name`: the name of the product
	* `api_url`: url to retrieve more data about this product
	* `image_small`: url to the small image
	* `image_medium`: url to the medium image
	* `image_large`: url to the large image
	* `deeplink_buy_url`: url used to buy a product
	* `price`: float notation of the price
	* `price_formatted`: pre-formatted price
	* `from_price`: float notation of the from price (in case of a sale)
	* `from_price_formatted`: pre-formatted from price (in case of a sale)
	* `shop_name`: name of the shop
	* `shop_name_normalized`: name of the shop, usable in urls
	* `shop_currency`: ISO currency used by the shop
	* `brand`: name of the brand
	* `brand_normalized`: name of the brand, usable in urls

-   `/product/:product_id/shop`
Returns a single shop in the same format as `/shop`.
-   `/product/:product_id/lookbook`
Returns a list in the same format as `/lookbook`.

-   `/lookbook`
Returns a list of multiple lookbooks with the following keys available:
	* `lookbook_id`: identifier
	* `api_url`: url to retrieve more data about this lookbook
	* `title`: the title of the lookbook
	* `description`: the description of the lookbook
	* `image_small`: url to the small image
	* `image_medium`: url to the medium image
	* `image_large`: url to the large image
	* `created_at`: date and time of creation
	* `total_price`: float notation of the total price
	* `total_price_formatted`: pre-formatted total price
	* `total_price_currency`: ISO currency used by the jpx
	
	The following filters are available.
	* `limit`: limits the query (default is 10)
	* `offset`: gives the query an offset (default is 0)
	* `query`: search query (string only)
	* `tags`: can be a comma separated string or an array
	* `sort`: can have these values: `relevance`, `popularity`, `date`
		Note that date is default, with the `query` filter relevance is default.

-   `/lookbook/:lookbook_id`
Returns a single lookbook in the same format as `/lookbook`.
-   `/lookbook/:lookbook_id/product`
Returns a list in the same format as `/product`.

-   `/shop`
Returns a list of multiple shops with the following keys available:
	* `shop_id`: identifier
	* `api_url`: url to retrieve more data about this shop
	* `name`: the name of this shop
	* `normalized_name`: name of the shop, usable in urls
	* `description`: description of the shop
	* `target_gender`: the gender where the shop targets on
	* `locale`: the locale of the shop
	* `currency`: the currency used by this shop
	* `image`: url to the shop's logo
	* `shop_url`: the url to the shop's homepage
	* `return_time`: maximum allowed return time
	* `return_free`: returns are free (true or false)
	* `return_address`: the address custumers can return their order to
	* `return_money_back`: money back after return (true or false)
	* `delivery_time`: usual time till delivery
	* `payment_method`: list of accepted payment methods
	* `delivery_costs`: usual delivery costs
-   `/shop/:shop_id`
Returns a single shop in the same format as `/shop`.

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

### Example code
The following code example explains how the api should be used. We start with including the autoloader and initializing the `Swyf_Api`. Then we set the base url and we are ready to start calling the api.

	<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);

	include_once 'vendor/autoload.php';

	$api = new Swyf_Api('jpx-JPXID', 'JPXSECRET');
	$api->setBaseUrl('https://api.shopwithyourfriends.com/api/v1/');

	$res = $api->get('product', array(
		'FILTER_NAME' => array(
			FILTER_VALUE
		),
		'FILTER_NAME' => FILTER_VALUE
	));
	?>

### Results
Results are returned as php arrays and objects, the JSON below is for readability.

	{
		data: {
			items: [ //contains the requested items
				{ … }, 
				{ … }
			],
			total_hits: TOTAL_RESULT_COUNT,
			facets: { //contains available facets
				FACET_NAME : {
					other_items_count : 0,
					items : [ //contains available filters
						{
							items : [ //contains available sub filters
								{
									normalized_name: NORMALIZED_NAME,
									count: NUMBER_OF_ITEMS_WITH_FILTER,
									is_selected: false,
									is_child_selected: false,
									display_name: DISPLAY_NAME,
									type: FACET_NAME,
									parent_normalized: PARENT_NORMALIZED_NAME,
									is_parent_selected: false
								},
								{ ... }
							],
							normalized_name: NORMALIZED_NAME,
							count: NUMBER_OF_ITEMS_WITH_FILTER,
							is_selected: false,
							is_child_selected: false,
							display_name: DISPLAY_NAME,
							type: FACET_NAME,
							parent_normalized: null,
							is_parent_selected: false
						},
						{ ... }
					]
				},
				{ ... }
			}
		},
		paging: {
			prev: null,
			next: 'https://api.shopwithyourfriends.com/api/v1/product?category=&query=&sort=&limit=10&offset=10'
		}
	}

# To be completed

- Explanation of resource types.
- Curl properties for each resource type list/get
- Filter parameters for product search.
- Filter parameters for lookbook search.
Outputs.
