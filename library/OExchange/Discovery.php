<?php
/**
 * OExchange PHP Library
 *
 * Copyright (c) 2010 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2010 Ramon Torres
 * @package OExchange
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * OExchange Discovery Utility class
 *
 * @package OExchange
 */
class OExchange_Discovery {

	const OEXCHANGE_RESIDENT_TARGET_REL = 'http://oexchange.org/spec/0.8/rel/resident-target';
	const OEXCHANGE_RELATED_TARGET_REL = 'http://oexchange.org/spec/0.8/rel/related-target';

	/**
	 * Performs a Host Discovery flow on a given host and returns a
	 * list of available Targets.
	 *
	 * @param string $host
	 * @return array
	 * @link http://www.oexchange.org/spec/#discovery-host
	 */
	public static function host($host) {
		$xrdUrl = 'http://' . rtrim($host, '/') . '/.well-known/host-meta';
		$xrd = XRD_Document::fromString(self::_retrieve($xrdUrl));

		$targets = array();
		foreach ($xrd->linksByRel(self::OEXCHANGE_RESIDENT_TARGET_REL) as $link) {
			try {
				$serviceXRD = XRD_Document::fromString(self::_retrieve($link->href));
				$targets[] = OExchange_Target::fromXRD($serviceXRD);
			} catch (XRD_Exception $e) {
			}
		}

		return $targets;
	}

	/**
	 * undocumented function
	 *
	 * @param string $url
	 * @return array
	 * @link http://www.oexchange.org/spec/#discovery-page
	 */
	public static function page($url) {
		$htmlDocument = new DOMDocument();
		$loaded = @$htmlDocument->loadHTML(self::_retrieve($url));
		if (!$loaded) {
			throw new OExchange_Exception("Failed to load {$url}");
		}

		$xpath = new DOMXpath($htmlDocument);

		$expression = "/html/head/link[@rel='" . self::OEXCHANGE_RELATED_TARGET_REL . "']";
		$links = $xpath->query($expression);
	
		$targets = array();
		foreach ($links as $link) {
			$serviceXRD = XRD_Document::fromString(self::_retrieve($link->getAttribute('href')));
			$targets[] = OExchange_Target::fromXRD($serviceXRD);
		}

		return $targets;
	}

	/**
	 * undocumented function
	 *
	 * @param string $url 
	 * @return string
	 * @throws OExchange_Exception
	 */
	protected static function _retrieve($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$result = curl_exec($ch);
		if ($result === false) {
			throw new OExchange_Exception(curl_error($ch));
		}

		curl_close($ch);
		return $result;
	}
}
