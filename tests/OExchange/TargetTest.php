<?php

require_once dirname(dirname(__FILE__)) . '/TestHelper.php';

class OExchange_TargetTest extends PHPUnit_Framework_TestCase {

	public function testToXRDDocumentGeneration() {
		$target = new OExchange_Target(array(
			'name'     => 'CoolService',
			'title'    => 'A cool service that accepts URLs',
			'subject'  => 'http://www.example.org/coolservice',
			'vendor'   => 'Examples Inc',
			'prompt'   => 'Share to CoolService',
			'offer'    => 'http://www.example.com/coolservice/offer.php',
			'icon'     => 'http://www.example.com/assets/icon.png',
			'icon32'   => 'http://www.example.com/assets/icon32.png',
		));

		$result = $target->toXRD()->toXML();
		$this->assertXmlStringEqualsXmlFile(TEST_DATA_PATH . '/coolservice.xrd', $result);
	}
}
