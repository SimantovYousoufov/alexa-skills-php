<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Request\IntentRequest;
use AlexaPHP\Security\RequestVerifier;
use AlexaPHP\Session\EphemeralSession;
use Illuminate\Http\Request;
use Mockery;

class AlexaIntentRequestTest extends TestCase
{
	public $config = [
		'cert_chain_url_header'         => 'SignatureCertChainUrl',
		'signature_header'              => 'Signature',
		'timestamp_delay_limit_seconds' => 150,
		'expect_scheme'                 => 'https',
		'expect_path_start_regexp'      => '/^\/echo.api/',
		'expect_port'                   => 443,
		'expect_host'                   => 's3.amazonaws.com',
		'expect_san'                    => 'echo-api.amazon.com',
		'encryption_method'             => 'sha1WithRSAEncryption',
		'application_id'                => 'arbitrary',
	];

	public function testItAcceptsAndSetsDependencies()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('all')->andReturn(['some' => 'input']);

		$alexa_request = new AlexaTestRequest($request, $this->config, $verifier, $session);

		$this->assertEquals($request, $alexa_request->getRequest());
		$this->assertEquals($verifier, $alexa_request->getVerifier());
		$this->assertEquals($session, $alexa_request->getSession());
		$this->assertEquals($this->config, $alexa_request->getConfig());
	}

	public function testItReturnsCorrectIntent()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('all')->andReturn(['some' => 'input']);
		$request->shouldReceive('input')->with('request.intent.name')->andReturn('SomeIntent');

		$alexa_request = new IntentRequest($request, $this->config, $verifier, $session);

		$this->assertEquals('SomeIntent', $alexa_request->getIntent()); // Not cached
		$this->assertEquals('SomeIntent', $alexa_request->getIntent()); // Cached
	}

	public function testItCanSetIntent()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('all')->andReturn(['some' => 'input']);
		$request->shouldReceive('input')->with('request.intent.name')->andReturn('SomeIntent');

		$alexa_request = new IntentRequest($request, $this->config, $verifier, $session);

		$alexa_request->setIntent('AnotherIntent');
		$this->assertEquals('AnotherIntent', $alexa_request->getIntent());
	}

	public function testItCanReturnIfIsLaunchRequest()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('all')->andReturn(['some' => 'input']);
		$request->shouldReceive('input')->with('request.intent.name')->andReturn('SomeIntent');

		$alexa_request = new IntentRequest($request, $this->config, $verifier, $session);

		$session->shouldReceive('isNew')->andReturn(true);

		$this->assertTrue($alexa_request->isLaunchRequest());
	}

	public function testItCanGetSlots()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$input = [
			'some' => 'input',
			'request'=> [
				'intent' => [
					'slots' => [
						'input' => [
							'here' => 'success!',
						],
					],
				]
			],
		];

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('all')->andReturn($input);

		$alexa_request = new IntentRequest($request, $this->config, $verifier, $session);

		$this->assertEquals($input['request']['intent']['slots'], $alexa_request->getSlots());
	}

	public function testItCanGetSlotByKey()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$input = [
			'some' => 'input',
			'request'=> [
				'intent' => [
					'slots' => [
						'input' => [
							'here' => 'success!',
						],
					],
				]
			],
		];

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('all')->andReturn($input);

		$alexa_request = new IntentRequest($request, $this->config, $verifier, $session);

		$this->assertEquals($input['request']['intent']['slots']['input'], $alexa_request->getSlot('input'));
	}

	public function testItReturnsCorrectRequestType()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$input = [
			'some' => 'input',
		];

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('all')->andReturn($input);

		$alexa_request = new IntentRequest($request, $this->config, $verifier, $session);

		$this->assertEquals('IntentRequest', $alexa_request->requestType());
	}
}
