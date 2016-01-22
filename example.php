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

// Include the PHP Battle.net API Library.
require_once( __DIR__ . '/BattleNetAPI.php' );

/**
 * An API key is required to interact with the Battle.net API services. If
 * you do not have an API key, you must first go to http://dev.battle.net
 * and create an account. Once you have created an account and registered
 * your application with Battle.net, you will be assigned a unique API key
 * that you can use to make requests to the various API endpoints to
 * retrieve data. Without an API key, you will not be able to get a valid
 * response from the API.
 */
BattleNetAPI::setKey( 'YOUR BATTLE NET API KEY HERE' );

/**
 * The data through the API is limited to the region that it is in. For
 * example, if you set the region as 'Europe', then you will only receive data
 * when making requests for the Europe services, battlegroups and realms.
 * 
 * For a complete listing of available regions go to:
 * https://dev.battle.net/docs/read/community_apis
 * Default: US
 */
BattleNetAPI::setRegion( 'US' );

/**
 * The locale works closely alongside with the region. The region determines
 * the locales that are available to you.
 * 
 * For a list of the available locales in a particular region go to:
 * https://dev.battle.net/docs/read/community_apis
 * Default: en_US
 */
BattleNetAPI::setLocale( 'en_US' );

/**
 * Sample request callback function.
 *
 * Your callback function is called after each request.
 *
 * Whether the request was successful or failed, it will still call this
 * function.
 * @param string   $url      The requested URL
 * @param string   $response The response of the cURL request
 * @param info     $info     Information regarding the cURL request
 * @param resource $handle   The cURL handle
 */
function myCallback( $url, $response, $info, $handle ) {
	echo '<h1>' . $url . '</h1>';
	echo $response . '<hr />';
}

/**
 * Set your callback function to be called after each request.
 *
 * There are two methods available to you depending on the scope of your
 * callback function.
 * 
 * You can set your callback for a global function using a string:
 * BattleNetAPI::setCallback( 'myCallback' );
 *
 * Or you can set your callback for a class method using an array:
 * BattleNetAPI::setCallback( array( 'myClass', 'myCallback' ) );
 *
 * Your callback function whether the request was successful or failed.
 */
BattleNetAPI::setCallback( 'myCallback' );

/**
 * Set your maximum number of simultaneous connections. Keep in mind, the higher
 * the number the more processing and memory usage and the quicker you can
 * reach your request limits resulting in requests with over query limit errors.
 * Default: 5
 */
BattleNetAPI::setMaxConnections( 5 );

/**
 * Depending on your server configuration, if you are going to be sending many
 * requests and can expect it to take more than 20 seconds or use a lot of
 * memory, you will want to use the BattleNetAPI::noTimeout() function to allow
 * the script to run continuously without any execution time or memory
 * limitations.
 */
//BattleNetAPI::noTimeout();

/**
 * Throttle the number of requests that can be made in a single second. If the
 * number specified below is reached, the server will sleep for one second and
 * reset the counter.
 * 
 * !!!! IT IS NOT RECOMMENDED TO USE THROTTLING IN A PRODUCTION ENVIRONMENT !!!!
 *
 * Note that this does NOT limit the exact number of requests that are sent, as
 * there is no method of determining when cURL actually sent the request. It can
 * only be handled on the receiving end once a request has given us a response
 * back. This number is only to better tune the number of requests that are
 * processed per second and not necessarily how many requests are sent.
 * Default: 80
 */
BattleNetAPI::setThrottlePerSecond( 80 );

/**
 * Throttle the number of requests that can be made in a single hour. If the
 * number specified below is reached, the server will sleep for one hour and
 * reset the counter.
 *
 * !!!! IT IS NOT RECOMMENDED TO USE THROTTLING IN A PRODUCTION ENVIRONMENT !!!!
 *
 * Note that this does NOT limit the exact number of requests that are sent, as
 * there is no method of determining when cURL actually sent the request. It can
 * only be handled on the receiving end, once a request has given us a response
 * back. This number is only to better tune the number of requests that are
 * processed per hour and not necessarily how many requests are sent.
 *
 * The throttle per hour setting is only useful for internal applications, such
 * as when making several thousand requests consecutively to build an item
 * database. Without a database or some other form of logging for multiple users
 * it's not possible to keep count of how many requests were made in the past
 * hour.
 * Default: 35000
 */
//BattlenetAPI::setThrottlePerHour( 35000 );

/**
 * Add all your Battle.net API requests.
 */
BattleNetAPI::addRequest( 'wow', 'item', array(
	'itemId' => 19019
) );

/**
 * Send the requests.
 */
BattleNetAPI::send();
