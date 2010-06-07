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
 * @package XRD
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * XRD Property
 *
 * @package default
 */
class XRD_Property {

	/**
	 * undocumented variable
	 *
	 * @var string
	 */
	public $type;

	/**
	 * undocumented variable
	 *
	 * @var string
	 */
	public $value;

	public function __construct($type = null, $value = null) {
		$this->type = $type;
		$this->value = $value;
	}

	/**
	 * undocumented function
	 *
	 * @param DOMNode $node 
	 * @return XRD_Property
	 */
	public static function fromDOMNode(DOMNode $node) {
		$property = new XRD_Property;
		$property->type = $node->attributes->getNamedItem('type')->value;
		$property->value = $node->textContent;

		return $property;
	}
}
