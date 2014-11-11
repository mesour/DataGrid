<?php

use Tester\Assert,
    DataGrid\Components;

$container = require_once __DIR__ . '/../bootstrap.php';

class LinkTest extends \Test\BaseTestCase {

	function testWebUrl() {
		$link3 = new Components\Link(array(
			Components\Link::HREF => 'http://www.google.com',
			Components\Link::USE_NETTE_LINK => FALSE
		));

		list($to_href) = $link3->create();
		Assert::type('string', $to_href);
		Assert::contains('http://', $to_href);
	}

}

$test = new LinkTest($container);
$test->run();