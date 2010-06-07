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
 * XRD Link
 *
 * @package default
 */
class XRD_Link {

	/**
	 * undocumented variable
	 *
	 * @var string
	 */
	public $rel;

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
	public $href;

	/**
	 * undocumented variable
	 *
	 * @var string
	 */
	public $template;

	/**
	 * Contructor
	 *
	 * @param array $attributes 
	 */
	public function __construct($attributes = array()) {
		foreach ($attributes as $name => $value) {
			$this->{$name} = $value;
		}
	}

	/**
	 * undocumented function
	 *
	 * @param DOMNode $node 
	 * @return XRD_Link
	 * @author ramon
	 */
	public static function fromDOMNode(DOMNode $node) {
		$link = new XRD_Link;

		foreach ($node->attributes as $attribute) {
			switch ($attribute->name) {
				case 'rel':
				case 'type':
				case 'href':
				case 'template':
					$link->{$attribute->name} = $attribute->value;
					break;

				default:
					break;
			}
		}

		return $link;
	}
}
