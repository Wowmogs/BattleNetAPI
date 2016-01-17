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

class BattleNetAPI_Wow_Character_Endpoint extends BattleNetAPI_Endpoint {
	function __construct( $params ) {
		if ( ! isset( $params['realm'], $params['characterName'] ) ) {
			throw new BattleNetAPI_Exception( 'The "/wow/character/" endpoint requires the parameters "realm" and "characterName".' );
		}

		$url = BattleNetAPI::getHost() . '/wow/character/' . rawurlencode( $params['realm'] ) . '/' . rawurlencode( $params['characterName'] );

		$validFields = array(
			'achievements', 'appearance', 'audit', 'feed', 'guild',
			'hunterPets', 'items', 'mounts', 'pets', 'petSlots', 'progression',
			'pvp', 'quests', 'reputation', 'statistics', 'stats', 'talents',
			'titles'
		);

		if ( isset( $params['fields'] ) ) {
			if ( is_string( $params['fields'] ) ) {
				$params['fields'] = explode( ',', $params['fields'] );
			}

			$params['fields'] = array_intersect( $params['fields'], $validFields );

			$url .= '?fields=' . implode( ',', $params['fields'] );
		}

		$this->url = $url;
	}
}
