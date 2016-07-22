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

	public function testItCanGetDetailsForCertPassedInParam()
	{
		$cert_data = $this->createACertificate();
		$cert = Certificate::createFromString($cert_data['cert']);

		$public_key = openssl_pkey_get_public($cert_data['cert']);
		$key_data = openssl_pkey_get_details($public_key);
		$this->assertEquals($key_data['key'], $cert->getDetails($public_key)['key']);
	}

	public function createACertificate()
	{
		$ssl_config_file = __DIR__ . '/openssl_test.cfn';
		$config_args = [
			'digest_alg' => RequestVerifier::ENCRYPTION_METHOD,
			'private_key_type' => OPENSSL_KEYTYPE_RSA,
			'req_extensions' => 'v3_req',
		];

		$extra_attributes = [
			 'subjectAltName' => "DNS:echo-api.amazon.com",
			 'basicConstraints' => "CA:FALSE",
			 'keyUsage' => "Digital Signature, Key Encipherment",
			 'extendedKeyUsage' => "TLS Web Server Authentication, TLS Web Client Authentication",
			 'certificatePolicies' => "Policy: 2.16.840.1.113733.1.7.54\n  CPS: https://d.symcb.com/cps\n  User Notice:\n    Explicit Text: https://d.symcb.com/rpa\n",
			 'authorityKeyIdentifier' => "keyid:0D:44:5C:16:53:44:C1:82:7E:1D:20:AB:25:F4:01:63:D8:BE:79:A5\n",
			 'crlDistributionPoints' => "\nFull Name:\n  URI:http://sd.symcb.com/sd.crl\n",
			 'authorityInfoAccess' => "OCSP - URI:http://sd.symcd.com\nCA Issuers - URI:http://sd.symcb.com/sd.crt\n",
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

		return compact('private_key', 'data', 'encrypted_data', 'signature', 'csr', 'cert');
	}

	public function testItCanReadRemoteCertificate()
	{
		//$temp_name = tempnam(sys_get_temp_dir(), 'alexa_test');
		//$certificate_data = $this->createACertificate();
		//file_put_contents($temp_name, $certificate_data['cert']);
		//
		//$request = Mockery::mock(Request::class);
		//
		//$request->shouldReceive('header')->with(RequestVerifier::CERT_CHAIN_URL_HEADER, null)
		//	->andReturn('https://s3.amazonaws.com/echo.api/echo-api-cert.pem');
		//
		//$request->shouldReceive('get')->with('request.timestamp', null)->andReturn(Carbon::now()->subSeconds(15)->toIso8601String());
		//
		//$verifier = new RequestVerifier($request);
		////$verifier->verifyCertificate("file://$temp_name");
		//$verifier->verifyCertificate("https://s3.amazonaws.com/echo.api/echo-api-cert.pem");
	}
}
