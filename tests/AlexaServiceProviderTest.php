<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Providers\AlexaServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Mockery;

class AlexaServiceProviderTest extends ApplicationTestCase
{
	protected $application_mock;

	protected $service_provider;

	public function setUp()
	{
		$this->setUpMocks();

		$this->service_provider = new AlexaServiceProvider($this->application_mock);

		parent::setUp();
	}

	protected function setUpMocks()
	{
		$this->application_mock = Mockery::mock(Application::class);
	}

	/**
	 * @test
	 */
	public function testItCanBeConstructed()
	{
		$this->assertInstanceOf(ServiceProvider::class, $this->service_provider);
	}

	/**
	 * @test
	 */
	public function testTheRegisterMethodDoesNothing()
	{
		$this->assertNull($this->service_provider->register());
	}

	public function testItBoots()
	{
		$dispatcher_mock = Mockery::mock(Dispatcher::class);

		$this->service_provider->boot($dispatcher_mock);
	}
}
