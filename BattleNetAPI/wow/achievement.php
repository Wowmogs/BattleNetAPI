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

class BattleNetAPI_Wow_Achievement_Endpoint extends BattleNetAPI_Endpoint {
	function __construct( $params ) {
		if ( ! isset( $params['id'] ) ) {
			throw new BattleNetAPI_Exception( 'The "/wow/achievement/" endpoint requires the parameter "id".' );
		}

		$this->url = BattleNetAPI::getHost() . '/wow/achievement/' . $params['id'];
	}
}
