<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Certificate\CertificateInterface;
use AlexaPHP\Certificate\Persistence\RemoteCertificatePersistence;
use AlexaPHP\Certificate\Certificate;
use Mockery;

class RemoteCertificatePersistenceTest extends TestCase
{
	const TEMP_DIR = __DIR__ . '/' . 'temp';

	public function testItCreatesNewCertificateFromRemoteLocation()
	{
		$persistence = new RemoteCertificatePersistence;
		$certificate = $persistence->getCertificateForKey('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');

		$this->assertTrue($certificate instanceof CertificateInterface);
	}

	public function testItDoesNotStore()
	{
		$persistence = new RemoteCertificatePersistence;

		$this->assertFalse($persistence->storeCertificateForKey('some_key', new Certificate(__DIR__ . '/test_cert.pem')));
	}

	public function testItDoesNotExpire()
	{
		$persistence = new RemoteCertificatePersistence;

		$this->assertFalse($persistence->expireCertificateForKey('some_key'));
	}
}
