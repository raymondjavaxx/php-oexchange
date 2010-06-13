<?php

require_once dirname(dirname(__FILE__)) . '/TestHelper.php';

class OExchange_DiscoveryTest extends PHPUnit_Framework_TestCase {

	public function testHost() {
		$targets = OExchange_Discovery::host('www.oexchange.org');

		$this->assertEquals(1, count($targets));

		$this->assertEquals('LinkEater', $targets[0]->name);
		$this->assertEquals('Send to LinkEater', $targets[0]->prompt);
		$this->assertEquals('A Service that Eats Links', $targets[0]->title);
		$this->assertEquals('http://www.oexchange.org/demo/linkeater', $targets[0]->subject);
		$this->assertEquals('http://www.oexchange.org/demo/linkeater/offer.php', $targets[0]->offer);
		$this->assertEquals('OExchange.org', $targets[0]->vendor);
		$this->assertEquals('http://www.oexchange.org/images/linkeater_16x16.png', $targets[0]->icon);
		$this->assertEquals('image/png', $targets[0]->iconType);
		$this->assertEquals('http://www.oexchange.org/images/linkeater_32x32.png', $targets[0]->icon32);
		$this->assertEquals('image/png', $targets[0]->icon32Type);
	}

	public function testPage() {
		$targets = OExchange_Discovery::page('http://www.oexchange.org/demo/');

		$this->assertEquals(1, count($targets));

		$this->assertEquals('LinkEater', $targets[0]->name);
		$this->assertEquals('Send to LinkEater', $targets[0]->prompt);
		$this->assertEquals('A Service that Eats Links', $targets[0]->title);
		$this->assertEquals('http://www.oexchange.org/demo/linkeater', $targets[0]->subject);
		$this->assertEquals('http://www.oexchange.org/demo/linkeater/offer.php', $targets[0]->offer);
		$this->assertEquals('OExchange.org', $targets[0]->vendor);
		$this->assertEquals('http://www.oexchange.org/images/linkeater_16x16.png', $targets[0]->icon);
		$this->assertEquals('image/png', $targets[0]->iconType);
		$this->assertEquals('http://www.oexchange.org/images/linkeater_32x32.png', $targets[0]->icon32);
		$this->assertEquals('image/png', $targets[0]->icon32Type);
	}
}
