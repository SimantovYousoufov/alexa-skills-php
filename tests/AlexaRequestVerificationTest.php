<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Persistence\RemoteCertificatePersistence;
use AlexaPHP\Utility\URL;
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

			$persistence = Mockery::mock(RemoteCertificatePersistence::class);

			// No exceptions should get thrown
			$verifier = new RequestVerifier($request, $persistence);
			$this->assertTrue($verifier->verifySignatureCertificateUrl(new URL($url)));

			unset($request);
			unset($persistence);
		}

		foreach ($should_fail_verification as $url => $failure) {
			$request = Mockery::mock(Request::class);

			$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)->andReturn($url);

			$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(
				Carbon::now()->subSeconds(2)->toIso8601String()
			);

			$persistence = Mockery::mock(RemoteCertificatePersistence::class);

			try {
				$verifier = new RequestVerifier($request, $persistence);
				$verifier->verifySignatureCertificateUrl(new URL($url));
				$this->fail("Expected exception for $failure but none thrown.");
			} catch (AlexaVerificationException $e) {
				if ($e->getMessage() !== "Request signature verification failed: $failure.") {
					$this->fail("Expected exception thrown to include $failure in message but it was not present.");
				}
			}

			unset($request);
			unset($persistence);
		}
	}

	public function testItThrowsExceptionOnInvalidTimestamp()
	{
		$request = Mockery::mock(Request::class);

		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
			->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');
		$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(null);

		$persistence = Mockery::mock(RemoteCertificatePersistence::class);

		$this->setExpectedException(AlexaVerificationException::class, 'Request verification failed: no timestamp specified.');
		$verifier = new RequestVerifier($request, $persistence);
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

		$persistence = Mockery::mock(RemoteCertificatePersistence::class);

		$this->setExpectedException(AlexaVerificationException::class, 'Request verification failed: timestamp is beyond tolerance limit.');
		$verifier = new RequestVerifier($request, $persistence);
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

		$persistence = Mockery::mock(RemoteCertificatePersistence::class);

		$this->setExpectedException(AlexaVerificationException::class, 'Request verification failed: negative tolerance is not allowed.');
		$verifier = new RequestVerifier($request, $persistence);
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

		$persistence = Mockery::mock(RemoteCertificatePersistence::class);

		$verifier = new RequestVerifier($request, $persistence);
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

		$persistence = Mockery::mock(RemoteCertificatePersistence::class);

		$this->setExpectedException(AlexaVerificationException::class, 'Request signature verification failed: Subject Alternative Names are invalid.');
		$verifier = new RequestVerifier($request, $persistence);
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

		$persistence = Mockery::mock(RemoteCertificatePersistence::class);

		$this->setExpectedException(AlexaVerificationException::class, 'Request signature verification failed: certificate timestamps are invalid.');
		$verifier = new RequestVerifier($request, $persistence);
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

		$persistence = Mockery::mock(RemoteCertificatePersistence::class);

		$this->setExpectedException(AlexaVerificationException::class, 'Request signature verification failed: certificate is invalid.');
		$verifier = new RequestVerifier($request, $persistence);
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

		$persistence = Mockery::mock(RemoteCertificatePersistence::class);

		$verifier = new RequestVerifier($request, $persistence);
		$this->assertTrue($verifier->verifyCertificate($certificate));
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

		$persistence = Mockery::mock(RemoteCertificatePersistence::class);

		$this->setExpectedException(AlexaVerificationException::class, 'Request signature verification failed: no signature present in header.');
		$verifier = new RequestVerifier($request, $persistence);
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

		$persistence = Mockery::mock(RemoteCertificatePersistence::class);

		$this->setExpectedException(AlexaVerificationException::class, 'Request signature verification failed: no signature present in header.');
		$verifier = new RequestVerifier($request, $persistence);
		$verifier->verifyCertificate($certificate);
	}

	public function testItVerifiesRequest()
	{
		$request = Mockery::mock(Request::class);
		$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
			->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');
		$request->shouldReceive('header')->with(RequestVerifier::SIGNATURE_HEADER, null)->andReturn('some_signature');
		$request->shouldReceive('getContent')->andReturn('some_content');
		$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(
			Carbon::now()->subSeconds(2)->toIso8601String()
		);

		$certificate = Mockery::mock(Certificate::class);
		$certificate->shouldReceive('hasValidDateConstraints')->andReturn(true);
		$certificate->shouldReceive('getSubjectAltNames')->andReturn(RequestVerifier::EXPECT_SAN);
		$certificate->shouldReceive('verify')->andReturn(true);

		$persistence = Mockery::mock(RemoteCertificatePersistence::class);
		$persistence->shouldReceive('getCertificateForURL')->with('https://s3.amazonaws.com/echo.api/echo-api-cert.pem')
			->andReturn($certificate);

		$verifier = new RequestVerifier($request, $persistence);
		$verifier->verifyRequest();
	}
}
