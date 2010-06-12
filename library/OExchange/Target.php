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
	const OEXCHANGE_OFFER_REL = 'http://www.oexchange.org/spec/0.8/rel/offer';
	const OEXCHANGE_RELATED_TARGET_REL = 'http://oexchange.org/spec/0.8/rel/related-target';

	const OEXCHANGE_TITLE_PROPERTY_TYPE = 'http://www.oexchange.org/spec/0.8/prop/title';
	const OEXCHANGE_VENDOR_PROPERTY_TYPE = 'http://www.oexchange.org/spec/0.8/prop/vendor';
	const OEXCHANGE_NAME_PROPERTY_TYPE = 'http://www.oexchange.org/spec/0.8/prop/name';
	const OEXCHANGE_PROMPT_PROPERTY_TYPE = 'http://www.oexchange.org/spec/0.8/prop/prompt';

	/**
	 * Human-readable long title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * undocumented variable
	 *
	 * @var string
	 */
	public $subject;

	/**
	 * OExchange offer endpoint
	 *
	 * @var string
	 */
	public $offer;

	/**
	 * undocumented variable
	 *
	 * @var string
	 */
	public $offerType;

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
	 * Contructor
	 *
	 * @param array $data 
	 */
	public function __construct($data = array()) {
		if (!empty($data)) {
			$defaults = array(
				'offerType' => 'text/html',
				'iconType' => 'image/png',
				'icon32Type' => 'image/png'
			);
			$data = array_merge($defaults, $data);
		}

		foreach ($data as $key => $value) {
			$this->{$key} = $value;
		}
	}

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

	public static function pageDiscovery($url) {
		$htmlDocument = new DOMDocument();
		$loaded = @$htmlDocument->loadHTMLFile($url);
		if (!$loaded) {
			throw new OExchange_Exception("Failed to load {$url}");
		}

		$xpath = new DOMXpath($htmlDocument);

		$expression = "/html/head/link[@rel='" . self::OEXCHANGE_RELATED_TARGET_REL . "']";
		$links = $xpath->query($expression);
	
		$targets = array();
		foreach ($links as $link) {
			$serviceXRD = XRD_Document::fromString(file_get_contents($link->getAttribute('href')));
			$targets[] = self::fromXRD($serviceXRD);
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
		$service->subject = $xrd->subject;

		$iconLink = $xrd->firstLinkByRel('icon');
		$service->icon = $iconLink->href;
		$service->iconType = $iconLink->type;

		$icon32Link = $xrd->firstLinkByRel('icon32');
		$service->icon32 = $icon32Link->href;
		$service->icon32Type = $icon32Link->type;

		$offerLink = $xrd->firstLinkByRel(self::OEXCHANGE_OFFER_REL);
		$service->offer = $offerLink->href;
		$service->offerType = $offerLink->type;

		$propertyMap = array(
			'title'  => self::OEXCHANGE_TITLE_PROPERTY_TYPE,
			'vendor' => self::OEXCHANGE_VENDOR_PROPERTY_TYPE,
			'name'   => self::OEXCHANGE_NAME_PROPERTY_TYPE,
			'prompt' => self::OEXCHANGE_PROMPT_PROPERTY_TYPE
		);

		foreach ($propertyMap as $propertyName => $propertyType) {
			$service->{$propertyName} = $xrd->firstPropertyByType($propertyType)->value;
		}

		return $service;
	}

	public function toXRD() {
		$xrd = new XRD_Document;
		$xrd->subject = $this->subject;

		$xrd->links[] = new XRD_Link(array(
			'rel'  => self::OEXCHANGE_OFFER_REL,
			'href' => $this->offer,
			'type' => $this->offerType
		));

		$xrd->links[] = new XRD_Link(array(
			'rel'  => 'icon',
			'href' => $this->icon,
			'type' => $this->iconType
		));

		if ($this->icon32) {
			$xrd->links[] = new XRD_Link(array(
				'rel'  => 'icon32',
				'href' => $this->icon32,
				'type' => $this->icon32Type
			));
		}

		$xrd->properties[] = new XRD_Property(self::OEXCHANGE_TITLE_PROPERTY_TYPE, $this->title);
		$xrd->properties[] = new XRD_Property(self::OEXCHANGE_VENDOR_PROPERTY_TYPE, $this->vendor);
		$xrd->properties[] = new XRD_Property(self::OEXCHANGE_NAME_PROPERTY_TYPE, $this->name);
		$xrd->properties[] = new XRD_Property(self::OEXCHANGE_PROMPT_PROPERTY_TYPE, $this->prompt);

		return $xrd;
	}
}
