<?php

namespace AlexaPHP\Tests;

use Mockery;

class AlexaConfigTest extends ApplicationTestCase
{
	public function testPackageConfigMatchesTestingConfig()
	{
		$package_config = include __DIR__ . '/../src/config/alexaphp.php';
		$this->assertEquals(config('alexaphp'), $package_config);
	}
}
