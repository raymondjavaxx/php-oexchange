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
 * XRD Document
 *
 * @package XRD
 */
class XRD_Document {

	public $subject;

	/**
	 * Array of XRD Links
	 *
	 * @var array
	 */
	public $links = array();

	/**
	 * Array of XRD Properties
	 *
	 * @var array
	 */
	public $properties = array();

	/**
	 * undocumented function
	 *
	 * @return string
	 */
	public function toXML() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$xrdNode = $dom->appendChild(new DOMElement('XRD'));
		$xrdNode->appendChild(new DOMElement('Subject', $this->subject));

		foreach ($this->links as $link) {
			$linkNode = $xrdNode->appendChild(new DOMElement('Link'));;
			$linkNode->setAttribute('rel', $link->rel);
			$linkNode->setAttribute('href', $link->href);
			if ($link->template) {
				$linkNode->setAttribute('template', $link->template);
			} else if ($link->type) {
				$linkNode->setAttribute('type', $link->type);
			}
		}

		foreach ($this->properties as $property) {
			$linkNode = $xrdNode->appendChild(new DOMElement('Property', $property->value));;
			$linkNode->setAttribute('type', $property->type);
		}

		return $dom->saveXML();
	}

	/**
	 * Loads an XRD document from string
	 *
	 * @param string $xrdString 
	 * @return XRD_Document
	 * @throws XRD_Exception
	 */
	public static function fromString($xrdString) {
		$dom = new DOMDocument;
		if (!$dom->loadXml($xrdString)) {
			throw new XRD_Exception("Could not load XRD from string");
		}
		
		return self::fromDOMDocument($dom);
	}

	/**
	 * Returns an XRD_Document from a DOMDocument object
	 *
	 * @param DOMDocument $dom 
	 * @return XRD_Document
	 * @throws XRD_Exception
	 */
	public static function fromDOMDocument(DOMDocument $dom) {
		$xrdNode = $dom->getElementsByTagName('XRD')->item(0);
		if ($xrdNode == NULL) {
			throw new XRD_Exception("The document you are trying doesn't contain an XRD root node");
		}

		$xrd = new XRD_Document;

		foreach ($xrdNode->childNodes as $childNode) {
			if (!isset($childNode->tagName)) {
				continue;
			}

			switch ($childNode->tagName) {
				case 'Link':
					$xrd->links[] = XRD_Link::fromDOMNode($childNode);
					break;

				case 'Subject':
					$xrd->subject = $childNode->textContent;
					break;

				case 'Property':
					$xrd->properties[] = XRD_Property::fromDOMNode($childNode);
					break;

				default:
					# code...
					break;
			}
		}

		return $xrd;
	}

	/**
	 * Returns a list of Links by $rel
	 *
	 * @param string $rel 
	 * @return array
	 */
	public function linksByRel($rel) {
		$results = array();
		foreach ($this->links as $link) {
			if ($link->rel == $rel) {
				$results[] = $link;
			}
		}
		return $results;
	}

	public function firstLinkByRel($rel) {
		$links = $this->linksByRel($rel);
		if (count($links) == 0) {
			return false;
		}

		return $links[0];
	}

	/**
	 * Return an array of Properties by $type
	 *
	 * @param string $type 
	 * @return array
	 */
	public function propertiesByType($type) {
		$results = array();
		foreach ($this->properties as $property) {
			if ($property->type == $type) {
				$results[] = $property;
			}
		}
		return $results;
	}

	public function firstPropertyByType($type) {
		$properties = $this->propertiesByType($type);
		if (count($properties) == 0) {
			return false;
		}

		return $properties[0];
	}
}
