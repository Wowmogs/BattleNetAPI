<?php
/**
 * PHP Battle.net API Library
 * https://github.com/Wowmogs/BattleNetAPI
 * 
 * Copyright (c) 2016 Wowmogs.com
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

class BattleNetAPI {
	/**
	 * An API key is required to interact with the Battle.net API services. If
	 * you do not have an API key, you must first go to http://dev.battle.net
	 * and create an account. Once you have created an account and registered
	 * your application with Battle.net, you will be assigned a unique API key
	 * that you can use to make requests to the various API endpoints to
	 * retrieve data. Without an API key, you will not be able to get a valid
	 * response from the API.
	 * @var string
	 */
	private static $key;

	/**
	 * The data through the API is limited to the region that it is in. For
	 * example, if you set the region as 'US', then you will only receive data
	 * when making requests for the US battlegroups and realms.
	 * 
	 * For a complete listing of available regions go to:
	 * https://dev.battle.net/docs/read/community_apis
	 * @var string
	 */
	private static $region = 'US';

	/**
	 * The locale works closely alongside with the region. The region determines
	 * the locales that are available to you.
	 * 
	 * For a list of the available locales in a particular region go to:
	 * https://dev.battle.net/docs/read/community_apis
	 * @var string
	 */
	private static $locale = 'en_US';

	/**
	 * The host contains the protocol, sub-domain, and domain of the Battle.net
	 * API service based on which region you set.
	 * @var string
	 */
	private static $host;

	/**
	 * Your callback function is called each time a request is processed.
	 * Whether the request was successful or failed, it will still call this
	 * function.
	 * @var callback function
	 */
	private static $callback;

	/**
	 * An array of the default cURL options to be used when making requests.
	 * 
	 * You are able to define your own cURL options using the setCurlOptions()
	 * function.
	 *
	 * CURLOPT_MAXCONNECTS is used to regulate how many asynchronous connections
	 * are allowed. The higher the number, the more requests that are executed
	 * simultaneously. 
	 *
	 * CURLOPT_RETURNTRANSFER must be set to TRUE in order to get the response
	 * of the requests to process in your callback function.
	 * 
	 * CURLOPT_SSL_VERIFYPEER must be set to FALSE since the Battle.net API
	 * service uses SSL encryption otherwise requests will not go through
	 * properly.
	 *
	 * More info on cURL options can be found at: http://php.net/curl_setopt
	 * @var array
	 */
	private static $curlOptions = array(
		CURLOPT_MAXCONNECTS    => 5,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_SSL_VERIFYPEER => FALSE
	);

	/**
	 * Contains an array of all the requests as BattleNetAPI_Request objects.
	 * @var array
	 */
	private static $requests;

	/**
	 * The total number of requests to be processed.
	 * @var integer
	 */
	private static $totalRequests;

	/**
	 * The maximum number of requests that can be made in a single second.
	 * @var integer
	 */
	private static $maxRequestsPerSecond = 0;

	/**
	 * The maximum number of requests that can be made in a single hour.
	 * @var integer
	 */
	private static $maxRequestsPerHour = 0;

	/**
	 * Total number of requests that were made this second.
	 * @var integer
	 */
	private static $totalRequestsThisSecond = 0;

	/**
	 * Total number of requests that were made this hour.
	 * @var integer
	 */
	private static $totalRequestsThisHour = 0;

	/**
	 * Contains a list of all the regions available to us.
	 *
	 * For a complete listing of available regions go to:
	 * https://dev.battle.net/docs/read/community_apis
	 * @var array
	 */
	private static $_regions = array(
		'US',
		'Europe',
		'Korea',
		'Taiwan',
		'China',
		'South East Asia'
	);

	/**
	 * Contains a list of all the regions available to us and their locales.
	 * 
	 * For a complete listing of available regions and locales go to:
	 * https://dev.battle.net/docs/read/community_apis
	 * @var array
	 */
	private static $_regionLocales = array(
		'US' => array(
			'en_US',
			'es_MX',
			'pt_BR'
		),
		'Europe' => array(
			'en_GB',
			'es_ES',
			'fr_FR',
			'ru_RU',
			'de_DE',
			'pt_PT',
			'it_IT'
		),
		'Korea' => array(
			'ko_KR'
		),
		'Taiwan' => array(
			'zh_TW'
		),
		'China' => array(
			'zh_CN'
		),
		'Sout East Asia' => array(
			'en_US'
		)
	);

	/**
	 * Allow the script to run continuously without any execution time or memory
	 * limitations. Useful for when making many requests.
	 */
	public static function noTimeout() {
		ini_set( 'max_execution_time', 0 );
		ini_set( 'memory_limit', -1 );
	}

	/**
	 * Sets the number of requests to throttle per second.
	 * @param int $requestsPerSecond Number of requests to throttle per second
	 */
	public static function setThrottlePerSecond( $requestsPerSecond ) {
		self::$maxRequestsPerSecond = $requestsPerSecond;
	}

	/**
	 * Whether or not requests should be throttled per second.
	 * @return boolean Returns TRUE if max requests per second is greater than 0
	 */
	private static function isThrottledPerSecond() {
		if ( self::$maxRequestsPerSecond > 0 ) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Sets the number of requests to throttle per hour.
	 * @param int $requestsPerHour Number of requests to throttle per hour
	 */
	public static function setThrottlePerHour( $requestsPerHour ) {
		self::$maxRequestsPerHour = $requestsPerHour;
	}

	/**
	 * Whether or not requests should be throttled per hour.
	 * @return boolean Returns TRUE if max requests per hour is greater than 0
	 */
	private static function isThrottledPerHour() {
		if ( self::$maxRequestsPerHour > 0 ) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Set the API key to authenticate with.
	 *
	 * An API key is required to interact with the Battle.net API services. If
	 * you do not have an API key, you must first go to http://dev.battle.net
	 * and create an account. Once you have created an account and registered
	 * your application with Battle.net, you will be assigned a unique API key
	 * that you can use to make requests to the various API endpoints to
	 * retrieve data. Without an API key, you will not be able to get a valid
	 * response from the API.
	 * @param string $key The API key to authenticate requests with.
	 */
	public static function setKey( $key ) {
		try {
			if ( ! is_string( $key ) ) {
				$keyVarType = gettype( $key );
				throw new BattleNetAPI_Exception( 'The method setKey() expects the parameter $key to be a string, ' . $keyVarType . ' given.' );
			}

			self::$key = $key;
		}
		catch ( BattleNetAPI_Exception $exception ) {
			$exception->showMessage();
		}
	}

	/**
	 * Returns the API key that was set.
	 * @return string The API key that was set.
	 */
	public static function getKey() {
		return self::$key;
	}

	/**
	 * Whether or not we have an API key set.
	 * @return boolean Returns TRUE if an API key is set, FALSE otherwise.
	 */
	private static function haveKey() {
		if ( is_null( self::$key ) || empty( self::$key ) ) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Set the region to make requests to.
	 * 
	 * The data through the API is limited to the region that it is in. For example,
	 * if you set the region as 'us', then you will only receive data when making
	 * requests for the US battlegroups and realms.
	 * 
	 * For a complete listing of available regions go to:
	 * https://dev.battle.net/docs/read/community_apis
	 * @param string $region 
	 */
	public static function setRegion( $region ) {
		try {
			if ( ! is_string( $region ) ) {
				$regionVarType = gettype( $region );
				throw new BattleNetAPI_Exception( 'The method setRegion() expects the parameter $region to be a string, ' . $regionVarType . ' given.' );
			}

			if ( ! in_array( $region, self::$_regions ) ) {
				throw new BattleNetAPI_Exception( 'Invalid region. Must be one of the following: "' . implode( '", "', self::$_regions ) . '"' );
			}

			switch ( $region ) {
				case 'US':
					self::$host = 'https://us.api.battle.net';
				break;
				case 'Europe':
					self::$host = 'https://eu.api.battle.net';
				break;
				case 'Korea':
					self::$host = 'https://kr.api.battle.net';
				break;
				case 'Taiwan':
					self::$host = 'https://tw.api.battle.net';
				break;
				case 'China':
					self::$host = 'https://api.battlenet.com.cn';
				break;
				case 'South East Asia':
					self::$host = 'https://sea.api.battle.net';
				break;
			}

			self::$region = $region;
		}
		catch ( BattleNetAPI_Exception $exception ) {
			$exception->showMessage();
		}
	}

	/**
	 * Get the host address of the API based on the region configured.
	 * @return string Host address of the API for the configured region.
	 */
	public static function getHost() {
		return self::$host;
	}

	/**
	 * Set the locale to return localized strings based on the region.
	 *
	 * Only certain localizations are available depending on the region.
	 * 
	 * For a complete list of available regions and locales go to:
	 * https://dev.battle.net/docs/read/community_apis
	 * @param string $locale The locale to use in responses.
	 */
	public static function setLocale( $locale ) {
		try {
			if ( is_null( self::$region ) ) {
				throw new BattleNetAPI_Exception( 'The method setRegion() must be called first before you can call setLocale().' );
			}

			if ( ! is_string( $locale ) ) {
				$localeVarType = gettype( $locale );
				throw new BattleNetAPI_Exception( 'The method setLocale() expects the parameter $locale to be a string, ' . $localeVarType . ' given.' );
			}

			if ( ! in_array( $locale, self::$_regionLocales[ self::$region ] ) ) {
				throw new BattleNetAPI_Exception( 'The locale "' . $locale . '" is not available in the "' . self::$region . '" region. Must be one of the following: ' . implode( ', ', self::$_regionLocales[ self::$region ] ) );
			}

			self::$locale = $locale;
		}
		catch ( BattleNetAPI_Exception $exception ) {
			$exception->showMessage();
		}
	}

	/**
	 * Get the configured locale.
	 * @return string Returns the configured locale.
	 */
	public static function getLocale() {
		return self::$locale;
	}

	/**
	 * Set the maximum number of asynchronous connections to be used in cURL
	 * requests.
	 * @param integer $maxConnections Max number of connections.
	 */
	public static function setMaxConnections( $maxConnections ) {
		try {
			if ( ! is_int( $maxConnections ) ) {
				$maxConnectionsVarType = gettype( $maxConnections );
				throw new BattleNetAPI_Exception( 'The method setMaxConnections() expects the parameter $maxConnections to be an integer, ' . $maxConnectionsVarType . ' given.' );
			}

			self::$curlOptions[ CURLOPT_MAXCONNECTS ] = $maxConnections;
		}
		catch ( BattleNetAPI_Exception $exception ) {
			$exception->showMessage();
		}
	}

	/**
	 * Set your own custom cURL options for when making requests.
	 *
	 * For a list of available cURL options go to: http://php.net/curl_setopt
	 * @param array $options An array of cURL option key value pairs.
	 */
	public static function setCurlOptions( $options ) {
		try {
			if ( ! is_array( $options ) ) {
				$optionsVarType = gettype( $options );
				throw new BattleNetAPI_Exception( 'The method setCurlOptions() expects the parameter $options to be an array, ' . $optionsVarType . ' given.' );
			}

			self::$curlOptions = ( self::$curlOptions + $options );
		}
		catch ( BattleNetAPI_Exception $exception ) {
			$exception->showMessage();
		}
	}

	/**
	 * Set a callback function to be called after each request.
	 *
	 * The callback function is passed four parameters:
	 * $url      - The requested URL
	 * $response - The response of the cURL request
	 * $info     - Information regarding the cURL request
	 * $handle   - The cURL handle
	 *
	 * Whether the request was successful or failed, it will still call this
	 * function.
	 * @param mixed $callback A callable function to be made after each request.
	 */
	public static function setCallback( $callback ) {
		try {
			if ( is_string( $callback ) && function_exists( $callback ) ) {
				self::$callback = $callback;
			} else if ( is_callable( $callback ) ) {
				self::$callback = $callback;
			} else {
				throw new BattleNetAPI_Exception( 'The method setCallback() expects the parameter $callback to be a callable function. The function "' . $callback . '()" does not exist or is not callable.' );
			}
		}
		catch ( BattleNetAPI_Exception $exception ) {
			$exception->showMessage();
		}
	}

	/**
	 * Get the configured callback.
	 * @return mixed The configured callback as a string or array.
	 */
	public static function getCallback() {
		return self::$callback;
	}

	/**
	 * Whether or not we have a callback configured.
	 * @return boolean Returns TRUE if a callback is configured, FALSE otherwise.
	 */
	public static function haveCallback() {
		if ( isset( self::$callback ) ) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Add a single Battle.net API request to the queue.
	 *
	 * The $service is determined by the first portion of the endpoint. The
	 * $endpoint is determined by the remaining portion of the endpoint.
	 *
	 * Depending on the Battle.net API endpoint will determine if you need to
	 * provide additional parameters in order to get a valid response.
	 *
	 * For example, if the endpoint is "/wow/mount/" then the $service
	 * would be "wow" and the $endpoint would be "mount". If the endpoint is
	 * "/wow/item/:itemid" then the $service would be "wow", the $endpoint
	 * would be "item", and the $params array would contain an "itemId" key
	 * value pair.
	 *
	 * For detailed information about the various Battle.net API services that
	 * are available please go to: https://dev.battle.net/io-docs
	 * @param string $service  The Battle.net API service to use
	 * @param string $endpoint The endpoint to make the request to
	 * @param array  $params   An optional array of additional parameters
	 */
	public static function addRequest( $service, $endpoint, $params = NULL ) {
		try {
			if ( ! is_string( $service ) ) {
				$serviceVarType = gettype( $service );
				throw new BattleNetAPI_Exception( 'The method addRequest() expects the parameter $service to be a string, ' . $serviceVarType . ' given.' );
			}

			if ( ! is_string( $endpoint ) ) {
				$endpointVarType = gettype( $endpoint );
				throw new BattleNetAPI_Exception( 'The method addRequest() expects the parameter $endpoint to be a string, ' . $endpointVarType . ' given.' );
			}

			if ( ! is_null( $params ) && ! is_array( $params ) ) {
				$paramsVarType = gettype( $params );
				throw new BattleNetAPI_Exception( 'The method addRequest() expects the optional parameter $params to be an array, ' . $paramsVarType . ' given.' );
			}

			self::$requests[] = new BattleNetAPI_Request( $service, $endpoint, $params );
		}
		catch ( BattleNetAPI_Exception $exception ) {
			$exception->showMessage();
		}
	}

	/**
	 * Determines if we have configured our API key, that we have requests to
	 * send, and processes them.
	 */
	public static function send() {
		try {
			if ( ! function_exists( 'curl_version' ) ) {
				throw new BattleNetAPI_Exception( 'It appears that cURL is not enabled. Please check your server configuration.' );
			}

			if ( ! self::haveKey() ) {
				throw new BattleNetAPI_Exception( 'An API key is required to interact with the Battle.net API services. If you do not have a key, please create one at <a href="https://dev.battle.net/" target="_blank">https://dev.battle.net/</a>.' );
			}

			self::$totalRequests = count( self::$requests );

			if ( self::$totalRequests == 0 ) {
				throw new BattleNetAPI_Exception( 'There were no requests to be sent.' );
			}

			self::processRequests();
		}
		catch ( BattleNetAPI_Exception $exception ) {
			$exception->showMessage();
		}
	}

	/**
	 * Uses cURL multi to simultaneously process our requests and run our
	 * callback function after each request has completed.
	 */
	private static function processRequests() {
		if ( self::$totalRequests > self::$curlOptions[ CURLOPT_MAXCONNECTS ] ) {
			$maxConnections = self::$curlOptions[ CURLOPT_MAXCONNECTS ];
		} else {
			$maxConnections = self::$totalRequests;
		}

		$multiHandler = curl_multi_init();
		$options      = self::$curlOptions;

		$now         = time();
		$secondSleep = FALSE;
		$hourSleep   = FALSE;

		$isThrottledPerSecond = self::isThrottledPerSecond();
		$isThrottledPerHour   = self::isThrottledPerHour();

		for ( $i = 0; $i < $maxConnections; $i++ ) {
			$requestHandle = curl_init();
			$requestUrl    = self::formatRequestUrl( self::$requests[ $i ]->getUrl() );

			$options[ CURLOPT_URL ] = $requestUrl;

			self::$totalRequestsThisSecond++;
			self::$totalRequestsThisHour++;

			curl_setopt_array( $requestHandle, $options );
			curl_multi_add_handle( $multiHandler, $requestHandle );
		}

		do {
			while ( ( $haveRequests = curl_multi_exec( $multiHandler, $running ) ) == CURLM_CALL_MULTI_PERFORM );

			if ( $haveRequests != CURLM_OK ) {
				break;
			}

			while ( $theRequest = curl_multi_info_read( $multiHandler ) ) {
				$requestInfo = curl_getinfo( $theRequest['handle'] );

				$response = curl_multi_getcontent( $theRequest['handle'] );

				if ( self::haveCallback() ) {
					call_user_func_array( self::getCallback(), array( $requestInfo['url'], $response, $requestInfo, $theRequest['handle'] ) );
				}

				if ( $i < self::$totalRequests ) {
					if ( $isThrottledPerSecond ) {
						if ( $secondSleep ) {
							$secondSleep = FALSE;
							sleep( 1 );
							$now = time();
							self::$totalRequestsThisSecond = 0;
						}

						if ( time() > $now ) {
							$now = time();
							self::$totalRequestsThisSecond = 0;
						}

						if ( self::$totalRequestsThisSecond == self::$maxRequestsPerSecond ) {
							$secondSleep = TRUE;
							self::$totalRequestsThisSecond = 0;
						}

						self::$totalRequestsThisSecond++;
					}

					if ( $isThrottledPerHour ) {
						if ( $hourSleep ) {
							$hourSleep = FALSE;
							sleep( 3600 );
							$now = time();
							self::$totalRequestsThisHour = 0;
						}

						if ( self::$totalRequestsThisHour == self::$maxRequestsPerHour ) {
							$hourSleep = TRUE;
							self::$totalRequestsThisHour = 0;
						}

						self::$totalRequestsThisHour++;
					}

					$requestHandle = curl_init();

					$options[ CURLOPT_URL ] = self::formatRequestUrl( self::$requests[ $i++ ]->getUrl() );

					curl_setopt_array( $requestHandle, $options );
					curl_multi_add_handle( $multiHandler, $requestHandle );
				}

				curl_multi_remove_handle( $multiHandler, $theRequest['handle'] );
			}
		} while ( $running );

		curl_multi_close( $multiHandler );
	}

	/**
	 * Format a request URL by appending the localization string and API key to
	 * the end of the URL.
	 * @param  string $url The URL to be formatted
	 * @return string      Formatted URL with localization string and API key
	 */
	private static function formatRequestUrl( $url ) {
		$params = array();

		$locale = self::getLocale();
		$key    = self::getKey();

		if ( $locale ) {
			$params['locale'] = $locale;
		}

		if ( $key ) {
			$params['apikey'] = $key;
		}

		if ( ! empty( $params ) ) {
			$queryString = http_build_query( $params );

			if ( strpos( $url, '?' ) ) {
				$url .= '&' . $queryString;
			} else {
				$url .= '?' . $queryString;
			}
		}

		return $url;
	}
}
