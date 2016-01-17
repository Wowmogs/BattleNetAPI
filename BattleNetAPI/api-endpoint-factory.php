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

class BattleNetAPI_Endpoint_Factory {
	public static function create( $service, $endpoint, $params ) {
		try {
			$class = self::getClassName( $service, $endpoint );

			if ( class_exists( $class ) ) {
				return new $class( $params );
			} else {
				$classFileLocation = self::getClassFileLocation( $service, $endpoint );

				if ( file_exists( $classFileLocation ) ) {
					require_once( $classFileLocation );

					return new $class( $params );
				} else {
					throw new BattleNetAPI_Exception( 'The service/endpoint combination "' . $service . '/' . $endpoint . '" does not exist or is not yet implemented.' );
				}
			}
		}
		catch ( BattleNetAPI_Exception $exception ) {
			$exception->showMessage();
		}
	}

	private static function getClassName( $service, $endpoint ) {
		$service  = ucfirst( strtolower( $service ) );
		$endpoint = str_replace( ' ', '_', ucwords( strtolower( str_replace( '/', ' ', $endpoint ) ) ) );

		$className = 'BattleNetAPI_' . $service . '_' . $endpoint . '_Endpoint';

		return $className;
	}

	private static function getClassFileLocation( $service, $endpoint ) {
		$service  = strtolower( $service );
		$endpoint = strtolower( str_replace( '/', '-', $endpoint ) );

		$fileLocation = __DIR__ . '/' . $service . '/' . $endpoint . '.php';

		return $fileLocation;
	}
}
