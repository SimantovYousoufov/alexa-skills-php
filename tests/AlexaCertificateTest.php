<?php

namespace AlexaPHP\Tests;

use AlexaPHP\RequestVerifier;
use Carbon\Carbon;
use AlexaPHP\Certificate\Certificate;
use AlexaPHP\Exception\AlexaCertificateException;
use Mockery;

class AlexaCertificateTest extends TestCase
{
	public function testItThrowsExceptionIfBothLocationAndContentAreNotProvided()
	{
		$this->setExpectedException(AlexaCertificateException::class, 'No valid location or data for certificate specified.');
		$cert = new Certificate;
	}

	public function testItCanConstructFromLocation()
	{
		$file = __DIR__ . '/test_cert.pem';
		$cert = new Certificate($file);
		$this->assertEquals('DNS:' . RequestVerifier::EXPECT_SAN, $cert->getSubjectAltNames());
	}

	public function testItCanConstructFromString()
	{
		$file = file_get_contents(__DIR__ . '/test_cert.pem');
		$cert = new Certificate(false, $file);
		$this->assertEquals('DNS:' . RequestVerifier::EXPECT_SAN, $cert->getSubjectAltNames());
	}

	public function testItCanCreateFromLocation()
	{
		$file = __DIR__ . '/test_cert.pem';
		$cert = Certificate::createFromLocation($file);
		$this->assertEquals('DNS:' . RequestVerifier::EXPECT_SAN, $cert->getSubjectAltNames());
	}

	public function testItCanCreateFromString()
	{
		$data = file_get_contents(__DIR__ . '/test_cert.pem');
		$cert = Certificate::createFromString($data);
		$this->assertEquals('DNS:' . RequestVerifier::EXPECT_SAN, $cert->getSubjectAltNames());
	}

	public function testItCanReadFileFromRemoteLocation()
	{
		$cert = new Certificate('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');
		$this->assertEquals('DNS:' . RequestVerifier::EXPECT_SAN, $cert->getSubjectAltNames());
	}

	public function testItCanGetSANs()
	{
		$file = __DIR__ . '/test_cert.pem';
		$cert = new Certificate($file);
		$this->assertEquals('DNS:' . RequestVerifier::EXPECT_SAN, $cert->getSubjectAltNames());
	}

	public function testItThrowsExceptionIfDataIsInvalid()
	{
		$file = 'junk data';

		$this->setExpectedException(AlexaCertificateException::class, 'Unable to read certificate data.');
		$cert = Certificate::createFromString($file);
	}

	public function testItThrowsExceptionILocationIsReturnsEmptyData()
	{
		$file = tempnam(sys_get_temp_dir(), 'alexa_test'); // empty file

		$this->setExpectedException(AlexaCertificateException::class, 'Unable to retrieve certificate.');
		$cert = new Certificate($file);
	}

	public function testItCanDetermineDateValidityForNow()
	{
		$cert_data = $this->createACertificate();

		$cert = Certificate::createFromString($cert_data['cert']);

		$this->assertTrue($cert->hasValidDateConstraints());
	}

	public function testItCanDetermineDateValidityForSomeTime()
	{
		$cert_data = $this->createACertificate();

		$cert = Certificate::createFromString($cert_data['cert']);

		$this->assertTrue($cert->hasValidDateConstraints(Carbon::now()->addDays(364)->getTimestamp()));
	}

	public function testItReturnsFalseIfDateNotWithinRange()
	{
		$cert_data = $this->createACertificate();

		$cert = Certificate::createFromString($cert_data['cert']);

		$this->assertFalse($cert->hasValidDateConstraints(Carbon::now()->addDays(366)->getTimestamp())); // outside of range
	}

	public function testItCanReturnPublicKey()
	{
		$cert_data = $this->createACertificate();
		$cert = Certificate::createFromString($cert_data['cert']);

		$public_key = openssl_pkey_get_public($cert_data['cert']);
		$key_data = openssl_pkey_get_details($public_key);
		$this->assertEquals($key_data['key'], $cert->publicKey());
	}

	public function testItCanGetDetailsForCert()
	{
		$cert_data = $this->createACertificate();
		$cert = Certificate::createFromString($cert_data['cert']);

		$public_key = openssl_pkey_get_public($cert_data['cert']);
		$key_data = openssl_pkey_get_details($public_key);
		$this->assertEquals($key_data['key'], $cert->getDetails()['key']);
	}

	public function testItThrowsExceptionOnGetDetailsForJunkData()
	{
		$cert_data = $this->createACertificate();
		$cert = Certificate::createFromString($cert_data['cert']);

		$this->setExpectedException(AlexaCertificateException::class, 'Unable to get details for certificate.');
		$cert->getDetails('junk key')['key'];
	}

	public function testItCanGetDetailsForCertPassedInParam()
	{
		$cert_data = $this->createACertificate();
		$cert = Certificate::createFromString($cert_data['cert']);

		$public_key = openssl_pkey_get_public($cert_data['cert']);
		$key_data = openssl_pkey_get_details($public_key);
		$this->assertEquals($key_data['key'], $cert->getDetails($public_key)['key']);
	}

	public function testItReturnsTrueOnVerifiedCertificate()
	{
		$cert_data = $this->createACertificate();
		$cert = Certificate::createFromString($cert_data['cert']);

		$this->assertTrue($cert->verify($cert_data['data'], $cert_data['signature'], RequestVerifier::ENCRYPTION_METHOD));
	}

	public function testItReturnsFalseOnUnverifiedCertificate()
	{
		$cert_data = $this->createACertificate();
		$new_cert_data = $this->createACertificate();
		$cert = Certificate::createFromString($cert_data['cert']);

		$this->assertFalse($cert->verify($cert_data['data'], $new_cert_data['signature'], RequestVerifier::ENCRYPTION_METHOD));
	}

	public function testItCanReturnParsedCertificate()
	{
		$cert_data = $this->createACertificate();
		$cert = Certificate::createFromString($cert_data['cert']);

		$this->assertEquals(openssl_x509_parse($cert_data['cert']), $cert->getParsedCertificate());
	}

	public function createACertificate()
	{
		$config_args = [
			'digest_alg' => RequestVerifier::ENCRYPTION_METHOD,
			'private_key_type' => OPENSSL_KEYTYPE_RSA,
			'req_extensions' => 'v3_req',
		];

		$private_key_resource = openssl_pkey_new($config_args);

		$data = 'some arbitrary data';
		$signature = '';
		$encrypted_data = '';

		// Create a signature in $signature
		openssl_sign($data, $signature, $private_key_resource, RequestVerifier::ENCRYPTION_METHOD);

		// Encrypt $data with the $private_key_resource. It can be decrypted with a public key, but can't be created with this signature
		openssl_private_encrypt($data, $encrypted_data, $private_key_resource);

		$csr_resource = openssl_csr_new(
			[
				'commonName' => 'echo-api.amazon.com',
			],
			$private_key_resource,
			$config_args
		);

		$cert_resource = openssl_csr_sign($csr_resource, null, $private_key_resource, 365, $config_args);

		$csr = '';
		$cert = '';
		$private_key = '';

		openssl_csr_export($csr_resource, $csr);
		openssl_x509_export($cert_resource, $cert);
		openssl_pkey_export($private_key_resource, $private_key);

		return compact('private_key', 'data', 'encrypted_data', 'signature', 'csr', 'cert', 'data');
	}

	public function testItReturnsCarbonStartDateForCertificate()
	{
		$cert_data = $this->createACertificate();
		$cert = Certificate::createFromString($cert_data['cert']);

		$parsed = openssl_x509_parse($cert_data['cert']);

		$this->assertEquals(Carbon::createFromTimestamp($parsed['validFrom_time_t']), $cert->getStartDate());
	}

	public function testItReturnsTimestampStartDateForCertificate()
	{
		$cert_data = $this->createACertificate();
		$cert = Certificate::createFromString($cert_data['cert']);

		$parsed = openssl_x509_parse($cert_data['cert']);

		$this->assertEquals($parsed['validFrom_time_t'], $cert->getStartDate(false));
	}

	public function testItReturnsCarbonEndDateForCertificate()
	{
		$cert_data = $this->createACertificate();
		$cert = Certificate::createFromString($cert_data['cert']);

		$parsed = openssl_x509_parse($cert_data['cert']);

		$this->assertEquals(Carbon::createFromTimestamp($parsed['validTo_time_t']), $cert->getEndDate());
	}

	public function testItReturnsTimestampEndDateForCertificate()
	{
		$cert_data = $this->createACertificate();
		$cert = Certificate::createFromString($cert_data['cert']);

		$parsed = openssl_x509_parse($cert_data['cert']);

		$this->assertEquals($parsed['validTo_time_t'], $cert->getEndDate(false));
	}
}
