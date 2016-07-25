<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Request\LaunchRequest;
use AlexaPHP\Security\RequestVerifier;
use AlexaPHP\Session\EphemeralSession;
use Illuminate\Http\Request;
use Mockery;

class AlexaLaunchRequestTest extends TestCase
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

		$alexa_request = new LaunchRequest($request, $this->config, $verifier, $session);

		$this->assertEquals($request, $alexa_request->getRequest());
		$this->assertEquals($verifier, $alexa_request->getVerifier());
		$this->assertEquals($session, $alexa_request->getSession());
		$this->assertEquals($this->config, $alexa_request->getConfig());
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

		$alexa_request = new LaunchRequest($request, $this->config, $verifier, $session);

		$this->assertEquals('LaunchRequest', $alexa_request->requestType());
	}

	public function testItReturnsNullForLastAction()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$input = [
			'some' => 'input',
		];

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('all')->andReturn($input);

		$alexa_request = new LaunchRequest($request, $this->config, $verifier, $session);

		$this->assertNull($alexa_request->lastAction());
	}
}
