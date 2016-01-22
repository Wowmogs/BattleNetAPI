# PHP Battle.net API Library
![Release](https://img.shields.io/github/release/Wowmogs/BattleNetAPI.svg) ![Downloads](https://img.shields.io/github/downloads/Wowmogs/BattleNetAPI/total.svg) ![Forks](https://img.shields.io/github/forks/Wowmogs/BattleNetAPI.svg) ![Stars](https://img.shields.io/github/stars/Wowmogs/BattleNetAPI.svg) ![License](https://img.shields.io/badge/license-MIT-blue.svg)

An object-oriented PHP library for communicating with the various Battle.net API services using authenticated asynchronous cURL requests.

### Table of Contents
- [Introduction](#introduction)
- [Usage](#usage)
  - [Set API Key](#set-api-key)
  - [Set Region](#set-region)
  - [Set Locale](#set-locale)
  - [Set Callback](#set-callback)
  - [Set Max Connections](#set-max-connections)
  - [No Timeout](#no-timeout)
  - [Throttle Requests](#throttle-requests)
  - [Add A Request](#add-a-request)
  - [Send The Requests](#send-the-requests)
  - [Available Services & Endpoints](#available-services--endpoints)
- [Changelog](#changelog)
- [License](#license)

## Introduction
Before we begin, in order to communicate with the Battle.net API services, **you must first [register](https://dev.battle.net) to get an API key.** Once you have created an account and registered your application with Battle.net, you can start making requests to the various Battle.net API services and endpoints.

## Usage
Please reference `example.php` for a simple implementation and additional usage documentation.

### Set API Key
An API key is required to interact with the Battle.net API services. If you do not have an API key, you must first go to <http://dev.battle.net> and create an account. Once you have created an account and registered your application with Battle.net, you will be assigned a unique API key that you can use to make requests to the various API services and endpoints. Without an API key, you will not be able to get a valid response from the API.

Example usage:
```
BattleNetAPI::setKey( 'YOUR BATTLE NET API KEY HERE' );
```

### Set Region
*Default: US*

The data through the API is limited to the region that it is in. For example, if you set the region as `Europe`, then you will only receive data when making requests for the Europe services, battlegroups and realms.

For a complete listing of available regions go to: <https://dev.battle.net/docs/read/community_apis>
```
BattleNetAPI::setRegion( 'US' );
```

### Set Locale
*Default: en_US*

The locale works closely alongside with the region. The region determines the locales that are available to you.

For a list of the available locales in a particular region go to: <https://dev.battle.net/docs/read/community_apis>

Example usage:
```
BattleNetAPI::setLocale( 'en_US' );
```

### Set Callback
Set your callback function to be called after each request.

There are two methods available to you depending on the scope of your callback function:

You can set your callback for a global function using a string:
`BattleNetAPI::setCallback( 'myCallback' );`

Or you can set your callback for a class method using an array:
`BattleNetAPI::setCallback( array( 'myClass', 'myCallback' ) );`

Your callback function is called each time a request is processed, whether the request was successful or failed.

Example callback:
```
function myCallback( $url, $response, $info, $handle ) {
	echo '<h1>' . $url . '</h1>';
	echo $response . '<hr />';
}
```
Example usage:
```
BattleNetAPI::setCallback( 'myCallback' );
```

### Set Max Connections
*Default: 5*

Set your maximum number of simultaneous connections. Keep in mind, the higher the number, the more processing and memory usage.

Example usage:
```
BattleNetAPI::setMaxConnections( 5 );
```

### No Timeout
Depending on your server configuration, if you are going to be sending many requests and can expect it to take more than 20 seconds or use a lot of memory, you will want to use the `BattleNetAPI::noTimeout()` function to allow the script to run continuously without any execution time or memory limitations.

Example usage:
```
BattleNetAPI::noTimeout();
```

### Throttle Requests
*IT IS NOT RECOMMENDED TO USE THROTTLING IN A PRODUCTION ENVIRONMENT.*

Each request that you make with your unique key it is counted towards the maximum number of requests you can make per second, and per hour.

As of January 21, 2016 with a "Basic Plan" you are limited to the following:

**100** Calls per second
**36,000** Calls per hour

If you use all 100 calls for every second consecutively, you will reach the 36,000 calls per hour limit in 6 minutes. Meaning, for the remaining 54 minutes you won't be able to make any requests without getting an error (i.e. `Account Over Queries Per Hour Limit`).

To help with these limitations, we have implemented two functions that you can use to fine tune how many requests you make per second and per hour.

**IMPORTANT:** Note that this does NOT limit the exact number of requests that are sent, as there is no method of determining when cURL actually sent the request. It can only be handled on the receiving end once a request has given us a response back. This number is only to better tune the number of requests that are processed per second and per hour and not necessarily how many requests are sent. Meaning, if you set this number to 100, you won't necessarily limit 100 requests being sent which is why it's a good idea to keep it a little under.

Throttle the number of requests processed per second (*Default: 80*):

Example usage:
```
BattleNetAPI::setThrottlePerSecond( 80 );
```

Throttle the number of requests processed per hour (*Default: 35000*):

Example usage:
```
BattleNetAPI::setThrottlePerHour( 35000 );
```

The throttle per hour setting is only useful for internal applications, such as when making several thousand requests consecutively to build an item database. Without a database or some other form of logging for multiple users it's not possible to keep count of how many requests were made in the past hour.

### Add A Request
Add a single Battle.net API request to the queue.

The `$service` is determined by the first portion of the endpoint. The `$endpoint` is determined by the remaining portion of the endpoint. Depending on the Battle.net API endpoint will determine if you need to provide additional parameters in order to get a valid response.

For example, if the endpoint is "/wow/mount/" then the `$service` would be "wow" and the `$endpoint` would be "mount". If the endpoint is "/wow/item/:itemid" then the `$service` would be "wow", the `$endpoint` would be "item", and the `$params` array would contain an "itemId" key value pair.

For detailed information about the various Battle.net API services that are available please go to: <https://dev.battle.net/io-docs>

Parameter | Type | Description
--- | --- | ---
`$service` | *string* | The Battle.net API service to use.
`$endpoint` | *string* | The endpoint to make the request to.
`$params` | *array* | An array of additional parameters for the endpoint.

Example usage:
```
BattleNetAPI::addRequest( 'wow', 'item', array(
	'itemId' => 19019
) );
```
*Did someone say [Thunderfury, Blessed Blade of the Windseeker]?*

### Send The Requests
When you've finished adding your requests, it's time to send them off!
```
BattleNetAPI::send();
```

### Available Services & Endpoints
Please visit <https://dev.battle.net/io-docs> for complete documentation of all available services and endpoints that are available and their specific parameters.


## Changelog

#### 1.0 - January 17, 2016
- Initial public release

## License
The PHP Battle.net API Library is released under the MIT License.

> Copyright (c) 2016 Wowmogs.com
> 
> Permission is hereby granted, free of charge, to any person obtaining a copy
> of this software and associated documentation files (the "Software"), to deal
> in the Software without restriction, including without limitation the rights
> to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
> copies of the Software, and to permit persons to whom the Software is
> furnished to do so, subject to the following conditions:
> 
> The above copyright notice and this permission notice shall be included in
> all copies or substantial portions of the Software.
> 
> THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
> IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
> FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
> AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
> LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
> OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
> THE SOFTWARE.

Made with ❤ from your friends at [Wowmogs.com](http://www.wowmogs.com).
