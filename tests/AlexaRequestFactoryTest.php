<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Exception\InvalidAlexaRequestException;
use AlexaPHP\Request\IntentRequest;
use AlexaPHP\Request\LaunchRequest;
use AlexaPHP\Request\RequestFactory;
use AlexaPHP\Request\SessionEndedRequest;
use AlexaPHP\Security\RequestVerifier;
use AlexaPHP\Session\EphemeralSession;
use Illuminate\Http\Request;
use Mockery;

class AlexaRequestFactoryTest extends ApplicationTestCase
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

	public function testFactoryManufacturesAlexaIntentRequest()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('input')->with('request.type', null)->andReturn('IntentRequest');
		$request->shouldReceive('all')->andReturn(['some' => 'input']);

		$config = config('alexaphp');

		$alexa_request = RequestFactory::makeRequest($request, $verifier, $session, $config);

		$this->assertTrue($alexa_request instanceof IntentRequest);
	}

	public function testFactoryManufacturesAlexaLaunchRequest()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('input')->with('request.type', null)->andReturn('LaunchRequest');
		$request->shouldReceive('all')->andReturn(['some' => 'input']);

		$config = config('alexaphp');

		$alexa_request = RequestFactory::makeRequest($request, $verifier, $session, $config);

		$this->assertTrue($alexa_request instanceof LaunchRequest);
	}

	public function testFactoryManufacturesAlexaSessionEndedRequest()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('input')->with('request.type', null)->andReturn('SessionEndedRequest');
		$request->shouldReceive('all')->andReturn(['some' => 'input']);

		$config = config('alexaphp');

		$alexa_request = RequestFactory::makeRequest($request, $verifier, $session, $config);

		$this->assertTrue($alexa_request instanceof SessionEndedRequest);
	}

	public function testItThrowsExceptionIfRequestTypeIsNull()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('input')->with('request.type', null)->andReturn(null);
		$request->shouldReceive('all')->andReturn(['some' => 'input']);

		$config = config('alexaphp');

		$this->setExpectedException(InvalidAlexaRequestException::class, 'Invalid request type specified.');
		$alexa_request = RequestFactory::makeRequest($request, $verifier, $session, $config);
	}

	public function testItThrowsExceptionIfRequestTypeIsEmptyString()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('input')->with('request.type', null)->andReturn('');
		$request->shouldReceive('all')->andReturn(['some' => 'input']);

		$config = config('alexaphp');

		$this->setExpectedException(InvalidAlexaRequestException::class, 'Invalid request type specified.');
		$alexa_request = RequestFactory::makeRequest($request, $verifier, $session, $config);
	}

	public function testItThrowsExceptionIfRequestTypeIsInvalid()
	{
		$session  = Mockery::mock(EphemeralSession::class);
		$verifier = Mockery::mock(RequestVerifier::class);
		$verifier->shouldReceive('verifyRequest');

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('input')->with('request.type', null)->andReturn('SomeWackyRequestType');
		$request->shouldReceive('all')->andReturn(['some' => 'input']);

		$config = config('alexaphp');

		$this->setExpectedException(InvalidAlexaRequestException::class, 'Invalid request type specified.');
		$alexa_request = RequestFactory::makeRequest($request, $verifier, $session, $config);
	}
}
