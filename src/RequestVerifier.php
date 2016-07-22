<?php

namespace AlexaPHP;

use Carbon\Carbon;
use Illuminate\Http\Request;
use AlexaPHP\Certificate\Certificate;
use AlexaPHP\Certificate\CertificateInterface;
use AlexaPHP\Utility\URL;
use AlexaPHP\Exception\AlexaVerificationException;

class RequestVerifier
{
	// @todo move all into config
	const CERT_CHAIN_URL_HEADER = 'SignatureCertChainUrl';

	const SIGNATURE_HEADER = 'Signature';

	const TIMESTAMP_DELAY_LIMIT_SECONDS = 150;

	const EXPECT_SCHEME = 'https';
	const EXPECT_PATH_START_REGEXP = '/^\/echo.api/';
	const EXPECT_PORT = 443;
	const EXPECT_HOST = 's3.amazonaws.com';
	const EXPECT_SAN = 'echo-api.amazon.com';

	const ENCRYPTION_METHOD = 'sha1WithRSAEncryption';

	/**
	 * Request container
	 *
	 * @var \Illuminate\Http\Request
	 */
	private $request;

	/**
	 * Cert URL
	 *
	 * @var string
	 */
	private $certificate_url;

	/**
	 * RequestVerifier constructor.
	 *
	 * @param \Illuminate\Http\Request $request
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * Verify that the request is coming from who we think it's coming from
	 *
	 * @return void
	 */
	public function verifyRequest()
	{
		$this->verifyTimestamp();
		$this->verifySignatureCertificateUrl();
		$this->verifyCertificate(new Certificate($this->certificate_url)); // @todo extract into provider
	}

	/**
	 * Verify that the certificate URL is valid
	 *
	 * @return void
	 */
	public function verifySignatureCertificateUrl()
	{
		$url_string = $this->request->header(self::CERT_CHAIN_URL_HEADER, null);

		if (is_null($url_string) || $url_string === '') {
			throw new AlexaVerificationException('Request signature verification failed: no URL specified.');
		}

		$url = new URL($url_string);

		$expectations = [
			'scheme' => self::EXPECT_SCHEME,
			'host'   => self::EXPECT_HOST,
		];

		// If a port is present, check it
		if (! is_null($url->port())) {
			$expectations['port'] = self::EXPECT_PORT;
		}

		if (! $url->matchesSchema($expectations)) {
			foreach ($expectations as $expectation => $value) {
				if (! $url->$expectation($value)) {
					throw new AlexaVerificationException("Request signature verification failed: $expectation.");
				}
			}
		}

		$path = $url->path();

		// $path should start with /echo.api/
		if (! preg_match(self::EXPECT_PATH_START_REGEXP, $path)) {
			throw new AlexaVerificationException('Request signature verification failed: path.');
		}

		$this->certificate_url = $url_string;
	}

	/**
	 * Verify that we're working with a valid certificate
	 *
	 * @param \AlexaPHP\Certificate\CertificateInterface $certificate
	 */
	public function verifyCertificate(CertificateInterface $certificate)
	{
		if (! $certificate->hasValidDateConstraints()) {
			throw new AlexaVerificationException('Request signature verification failed: certificate timestamps are invalid.');
		}

		if (! $this->containsSubjectAltName($certificate)) {
			throw new AlexaVerificationException('Request signature verification failed: Subject Alternative Names are invalid.');
		}

		$signature = $this->getSignatureFromRequest();
		$data      = $this->getRequestBody();

		$is_valid = $certificate->verify($data, $signature, self::ENCRYPTION_METHOD);

		if (! $is_valid) {
			throw new AlexaVerificationException('Request signature verification failed: certificate is invalid.');
		}
	}

	/**
	 * Verify the timestamp on the request
	 */
	public function verifyTimestamp()
	{
		$timestamp = $this->request->get('request.timestamp', null);

		if (is_null($timestamp) || $timestamp === '') {
			throw new AlexaVerificationException('Request verification failed: no timestamp specified.');
		}

		$time_diff = Carbon::parse($timestamp)->diffInSeconds(Carbon::now(), false);

		if ($time_diff > self::TIMESTAMP_DELAY_LIMIT_SECONDS) {
			throw new AlexaVerificationException('Request verification failed: timestamp is beyond tolerance limit.');
		}

		if ($time_diff < 0) {
			throw new AlexaVerificationException('Request verification failed: negative tolerance is not allowed.');
		}
	}

	/**
	 * Extract the signature from a request
	 *
	 * @return string
	 */
	protected function getSignatureFromRequest()
	{
		$signature = $this->request->header(self::SIGNATURE_HEADER, null);

		if (is_null($signature) || $signature === '') {
			throw new AlexaVerificationException('Request signature verification failed: no signature present in header.');
		}

		return base64_decode($signature);
	}

	/**
	 * Extract the request body from the request
	 *
	 * @return resource|string
	 */
	protected function getRequestBody()
	{
		return $this->request->getContent();
	}

	/**
	 * Determine whether the SAN on the cert contains what we expect it to contain
	 *
	 * @param \AlexaPHP\Certificate\CertificateInterface $certificate
	 * @return bool
	 */
	protected function containsSubjectAltName(CertificateInterface $certificate)
	{
		return strpos($certificate->getSubjectAltNames(), self::EXPECT_SAN) !== false;
	}
}
