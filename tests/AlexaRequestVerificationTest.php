<?php

namespace AlexaPHP\Tests;

use Carbon\Carbon;
use Illuminate\Http\Request;
use AlexaPHP\Certificate\Certificate;
use AlexaPHP\RequestVerifier;
use Mockery;
use AlexaPHP\Exception\AlexaVerificationException;

class AlexaRequestVerificationTest extends TestCase
{
	public function testItValidatesSignatureCertificateURL()
	{
		$should_pass_verification = [
			'https://s3.amazonaws.com/echo.api/echo-api-cert.pem',
			'https://s3.amazonaws.com:443/echo.api/echo-api-cert.pem',
			'https://s3.amazonaws.com/echo.api/../echo.api/echo-api-cert.pem',
		];

		$should_fail_verification = [
			//null                                                      => 'no URL specified',
			'http://s3.amazonaws.com/echo.api/echo-api-cert.pem'      => 'scheme',
			'https://notamazon.com/echo.api/echo-api-cert.pem'        => 'host',
			'https://s3.amazonaws.com/EcHo.aPi/echo-api-cert.pem'     => 'path',
			'https://s3.amazonaws.com/invalid.path/echo-api-cert.pem' => 'path',
			'https://s3.amazonaws.com:563/echo.api/echo-api-cert.pem' => 'port',
		];

		foreach ($should_pass_verification as $url) {
			$request = Mockery::mock(Request::class);

			$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)->andReturn($url);

			$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(
				Carbon::now()->subSeconds(2)->toIso8601String()
			);

			// No exceptions should get thrown
			$verifier = new RequestVerifier($request);
			$this->assertTrue($verifier->verifySignatureCertificateUrl());

			unset($request);
		}

		foreach ($should_fail_verification as $url => $failure) {
			$request = Mockery::mock(Request::class);

			$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)->andReturn($url);

			$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(
				Carbon::now()->subSeconds(2)->toIso8601String()
			);

			try {
				$verifier = new RequestVerifier($request);
				$verifier->verifySignatureCertificateUrl();
				$this->fail("Expected exception for $failure but none thrown.");
			} catch (AlexaVerificationException $e) {
				if ($e->getMessage() !== "Request signature verification failed: $failure.") {
					$this->fail("Expected exception thrown to include $failure in message but it was not present.");
				}
			}

			unset($request);
		}
	}

	public function testItThrowsExceptionOnInvalidTimestamp()
	{
		$request = Mockery::mock(Request::class);

		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
			->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');

		$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(null);

		$this->setExpectedException(AlexaVerificationException::class, 'Request verification failed: no timestamp specified.');
		$verifier = new RequestVerifier($request);
		$verifier->verifyTimestamp();
	}

	public function testItThrowsExceptionIfTimestampIsPassedDelayTime()
	{
		$request = Mockery::mock(Request::class);

		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
			->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');

		$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(
			Carbon::now()->subSeconds(151)->toIso8601String()
		);

		$this->setExpectedException(AlexaVerificationException::class, 'Request verification failed: timestamp is beyond tolerance limit.');
		$verifier = new RequestVerifier($request);
		$verifier->verifyTimestamp();
	}

	public function testItThrowsExceptionIfTimestampIsInTheFuture()
	{
		$request = Mockery::mock(Request::class);

		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
			->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');

		$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(
			Carbon::now()->addSeconds(100)->toIso8601String()
		);

		$this->setExpectedException(AlexaVerificationException::class, 'Request verification failed: negative tolerance is not allowed.');
		$verifier = new RequestVerifier($request);
		$verifier->verifyTimestamp();
	}

	public function testItValidatesIfTimestampIsWithinDelayTime()
	{
		$request = Mockery::mock(Request::class);

		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
			->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');

		$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(
			Carbon::now()->subSeconds(149)->toIso8601String()
		);

		$verifier = new RequestVerifier($request);
		$this->assertTrue($verifier->verifyTimestamp());
	}

	public function testItThrowsExceptionOnInvalidSAN()
	{
		$request = Mockery::mock(Request::class);
		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
			->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');
		$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(
			Carbon::now()->subSeconds(15)->toIso8601String()
		);

		$certificate = Mockery::mock(Certificate::class);
		$certificate->shouldReceive('hasValidDateConstraints')->andReturn(true);
		$certificate->shouldReceive('getSubjectAltNames')->andReturn('DNS:not.amazon.echo.api');

		$this->setExpectedException(AlexaVerificationException::class, 'Request signature verification failed: Subject Alternative Names are invalid.');
		$verifier = new RequestVerifier($request);
		$verifier->verifyCertificate($certificate);
	}

	public function testItThrowsExceptionOnInvalidCertificateDates()
	{
		$request = Mockery::mock(Request::class);
		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
			->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');
		$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(
			Carbon::now()->subSeconds(15)->toIso8601String()
		);

		$certificate = Mockery::mock(Certificate::class);
		$certificate->shouldReceive('hasValidDateConstraints')->andReturn(false);

		$this->setExpectedException(AlexaVerificationException::class, 'Request signature verification failed: certificate timestamps are invalid.');
		$verifier = new RequestVerifier($request);
		$verifier->verifyCertificate($certificate);
	}

	public function testItThrowsExceptionOnInvalidCertificate()
	{
		$request = Mockery::mock(Request::class);
		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
			->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');
		$request->shouldReceive('header')->with(RequestVerifier::SIGNATURE_HEADER, null)->andReturn('some_signature');
		$request->shouldReceive('getContent')->andReturn('some_content');

		$certificate = Mockery::mock(Certificate::class);
		$certificate->shouldReceive('hasValidDateConstraints')->andReturn(true);
		$certificate->shouldReceive('getSubjectAltNames')->andReturn(RequestVerifier::EXPECT_SAN);
		$certificate->shouldReceive('verify')->andReturn(false);

		$this->setExpectedException(AlexaVerificationException::class, 'Request signature verification failed: certificate is invalid.');
		$verifier = new RequestVerifier($request);
		$verifier->verifyCertificate($certificate);
	}

	public function testItVerifiesCertificate()
	{
		$request = Mockery::mock(Request::class);
		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
			->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');
		$request->shouldReceive('header')->with(RequestVerifier::SIGNATURE_HEADER, null)->andReturn('some_signature');
		$request->shouldReceive('getContent')->andReturn('some_content');

		$certificate = Mockery::mock(Certificate::class);
		$certificate->shouldReceive('hasValidDateConstraints')->andReturn(true);
		$certificate->shouldReceive('getSubjectAltNames')->andReturn(RequestVerifier::EXPECT_SAN);
		$certificate->shouldReceive('verify')->andReturn(true);

		$verifier = new RequestVerifier($request);
		$this->assertTrue($verifier->verifyCertificate($certificate));
	}


		public function testItThrowsExceptionOnNullCertificateURL()
	{
		$request = Mockery::mock(Request::class);

		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)->andReturn(null);

		$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(
			Carbon::now()->subSeconds(2)->toIso8601String()
		);

		$this->setExpectedException(AlexaVerificationException::class, 'Request signature verification failed: no URL specified.');
		$verifier = new RequestVerifier($request);
		$verifier->verifySignatureCertificateUrl();
	}

	public function testItThrowsExceptionCertificateURLIsEmptyString()
	{
		$request = Mockery::mock(Request::class);

		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)->andReturn('');

		$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(
			Carbon::now()->subSeconds(2)->toIso8601String()
		);

		$this->setExpectedException(AlexaVerificationException::class, 'Request signature verification failed: no URL specified.');
		$verifier = new RequestVerifier($request);
		$verifier->verifySignatureCertificateUrl();
	}

	public function testItThrowsExceptionIfSignatureIsNull()
	{
		$request = Mockery::mock(Request::class);
		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
			->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');
		$request->shouldReceive('header')->with(RequestVerifier::SIGNATURE_HEADER, null)->andReturn(null);
		$request->shouldReceive('getContent')->andReturn('some_content');

		$certificate = Mockery::mock(Certificate::class);
		$certificate->shouldReceive('hasValidDateConstraints')->andReturn(true);
		$certificate->shouldReceive('getSubjectAltNames')->andReturn(RequestVerifier::EXPECT_SAN);

		$this->setExpectedException(AlexaVerificationException::class, 'Request signature verification failed: no signature present in header.');
		$verifier = new RequestVerifier($request);
		$verifier->verifyCertificate($certificate);
	}

	public function testItThrowsExceptionIfSignatureIsEmptyString()
	{
		$request = Mockery::mock(Request::class);
		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
			->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');
		$request->shouldReceive('header')->with(RequestVerifier::SIGNATURE_HEADER, null)->andReturn('');
		$request->shouldReceive('getContent')->andReturn('some_content');

		$certificate = Mockery::mock(Certificate::class);
		$certificate->shouldReceive('hasValidDateConstraints')->andReturn(true);
		$certificate->shouldReceive('getSubjectAltNames')->andReturn(RequestVerifier::EXPECT_SAN);

		$this->setExpectedException(AlexaVerificationException::class, 'Request signature verification failed: no signature present in header.');
		$verifier = new RequestVerifier($request);
		$verifier->verifyCertificate($certificate);
	}
}
