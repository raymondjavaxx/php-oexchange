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
 * OExchange Target
 *
 * @package OExchange
 */
class OExchange_Target {

	const OEXCHANGE_REL = 'http://oexchange.org/spec/0.8/rel/resident-target';

	const OEXCHAGE_TITLE_PROPERTY_TYPE = 'http://www.oexchange.org/spec/0.8/prop/title';
	const OEXCHAGE_VENDOR_PROPERTY_TYPE = 'http://www.oexchange.org/spec/0.8/prop/vendor';
	const OEXCHAGE_NAME_PROPERTY_TYPE = 'http://www.oexchange.org/spec/0.8/prop/name';
	const OEXCHAGE_PROMPT_PROPERTY_TYPE = 'http://www.oexchange.org/spec/0.8/prop/prompt';

	/**
	 * Human-readable long title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * OExchange offer endpoint
	 *
	 * @var string
	 */
	public $offer;

	/**
	 * Name of service vendor
	 *
	 * @var string
	 */
	public $vendor;

	/**
	 * Name of the service
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Human-readable call to action for sending links to this target
	 *
	 * @var string
	 */
	public $prompt;

	/**
	 * Service icon URL
	 *
	 * @var string
	 */
	public $icon;

	/**
	 * Internet media type of service icon
	 *
	 * @var string
	 */
	public $iconType;

	/**
	 * 32x32 service icon URL
	 *
	 * @var string
	 */
	public $icon32;

	/**
	 * Internet media type of 32x32 icon
	 *
	 * @var string
	 */
	public $icon32Type;

	/**
	 * Performs a Host Discovery flow on a given host and returns a
	 * list of available Targets.
	 *
	 * @param string $host 
	 * @return array
	 */
	public static function discover($host) {
		$xrdUrl = 'http://' . rtrim($host, '/') . '/.well-known/host-meta';

		//TODO: use php-curl
		$xrd = XRD_Document::fromString(file_get_contents($xrdUrl));

		$targets = array();

		foreach ($xrd->linksByRel(self::OEXCHANGE_REL) as $link) {
			try {
				$serviceXRD = XRD_Document::fromString(file_get_contents($link->href));
				$targets[] = self::fromXRD($serviceXRD);
			} catch (XRD_Exception $e) {
			}
		}

		return $targets;
	}

	/**
	 * undocumented function
	 *
	 * @param XRD_Document $xrd 
	 * @return OExchange_Service
	 */
	public static function fromXRD(XRD_Document $xrd) {
		$service = new OExchange_Target;
		$service->offer = $xrd->subject;

		$iconLink = $xrd->firstLinkByRel('icon');
		$service->icon = $iconLink->href;
		$service->iconType = $iconLink->type;

		$icon32Link = $xrd->firstLinkByRel('icon32');
		$service->icon32 = $icon32Link->href;
		$service->icon32Type = $icon32Link->type;

		$propertyMap = array(
			'title'  => self::OEXCHAGE_TITLE_PROPERTY_TYPE,
			'vendor' => self::OEXCHAGE_VENDOR_PROPERTY_TYPE,
			'name'   => self::OEXCHAGE_NAME_PROPERTY_TYPE,
			'prompt' => self::OEXCHAGE_PROMPT_PROPERTY_TYPE
		);

		foreach ($propertyMap as $propertyName => $propertyType) {
			$service->{$propertyName} = $xrd->firstPropertyByType($propertyType)->value;
		}

		return $service;
	}
}
