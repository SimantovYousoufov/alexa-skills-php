<?php

namespace AlexaPHP\Security;

use AlexaPHP\Persistence\CertificatePersistenceInterface;
use AlexaPHP\Utility\URLInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use AlexaPHP\Certificate\CertificateInterface;
use AlexaPHP\Utility\URL;
use AlexaPHP\Exception\AlexaVerificationException;

class RequestVerifier implements RequestVerifierInterface
{
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
	 * Persistence for certificates
	 *
	 * @var \AlexaPHP\Persistence\CertificatePersistenceInterface
	 */
	private $persistence;

	/**
	 * Application configuration
	 *
	 * @var array
	 */
	private $config;

	/**
	 * RequestVerifier constructor.
	 *
	 * @param \Illuminate\Http\Request                              $request
	 * @param array                                                 $config
	 * @param \AlexaPHP\Persistence\CertificatePersistenceInterface $persistence
	 */
	public function __construct(Request $request, array $config, CertificatePersistenceInterface $persistence)
	{
		$this->request     = $request;
		$this->persistence = $persistence;
		$this->config      = $config;
	}

	/**
	 * Verify that the request is coming from who we think it's coming from
	 *
	 * @return void
	 */
	public function verifyRequest()
	{
		$this->verifyApplicationId();
		$this->verifyTimestamp();
		$this->verifySignatureCertificateUrl($this->getSignatureCertificateUrl());

		$certificate = $this->persistence->getCertificateForURL($this->certificate_url);
		$this->verifyCertificate($certificate);
	}

	/**
	 * Verify that the certificate URL is valid
	 *
	 * @param \AlexaPHP\Utility\URLInterface $url
	 * @return bool
	 */
	public function verifySignatureCertificateUrl(URLInterface $url)
	{
		$expectations = [
			'scheme' => $this->config['expect_scheme'],
			'host'   => $this->config['expect_host'],
		];

		// If a port is present, check it
		if (! is_null($url->port())) {
			$expectations['port'] = $this->config['expect_port'];
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
		if (! preg_match($this->config['expect_path_start_regexp'], $path)) {
			throw new AlexaVerificationException('Request signature verification failed: path.');
		}

		$this->certificate_url = $url->originalUrl();

		return true;
	}

	/**
	 * Verify that we're working with a valid certificate
	 *
	 * @param \AlexaPHP\Certificate\CertificateInterface $certificate
	 * @return bool
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

		$is_valid = $certificate->verify($data, $signature, $this->config['encryption_method']);

		if (! $is_valid) {
			throw new AlexaVerificationException('Request signature verification failed: certificate is invalid.');
		}

		return true;
	}

	/**
	 * Verify the timestamp on the request
	 *
	 * @return bool
	 */
	public function verifyTimestamp()
	{
		$timestamp = $this->request->get('request.timestamp', null);

		if (is_null($timestamp) || $timestamp === '') {
			throw new AlexaVerificationException('Request verification failed: no timestamp specified.');
		}

		$time_diff = Carbon::parse($timestamp)->diffInSeconds(Carbon::now(), false);

		if ($time_diff > $this->config['timestamp_delay_limit_seconds']) {
			throw new AlexaVerificationException('Request verification failed: timestamp is beyond tolerance limit.');
		}

		if ($time_diff < 0) {
			throw new AlexaVerificationException('Request verification failed: negative tolerance is not allowed.');
		}

		return true;
	}

	/**
	 * Verify that the request is for our application
	 *
	 * @return bool
	 */
	public function verifyApplicationId()
	{
		if ($this->getApplicationId() !== $this->config['application_id']) {
			throw new AlexaVerificationException('Request verification failed: invalid application ID.');
		}

		return true;
	}

	/**
	 * Extract the application ID from the request
	 *
	 * @return string
	 */
	protected function getApplicationId()
	{
		$application_id = $this->request->get('session.application.applicationId', null);

		if (is_null($application_id) || $application_id === '') {
			throw new AlexaVerificationException('Request verification failed: application ID not present in request.');
		}

		return $application_id;
	}

	/**
	 * Extract the signature from a request
	 *
	 * @return string
	 */
	protected function getSignatureFromRequest()
	{
		$signature = $this->request->header($this->config['signature_header'], null);

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
		return strpos($certificate->getSubjectAltNames(), $this->config['expect_san']) !== false;
	}

	/**
	 * Extract the signature certificate URL from the request header
	 *
	 * @return \AlexaPHP\Utility\URL
	 */
	protected function getSignatureCertificateUrl()
	{
		$url_string = $this->request->header($this->config['cert_chain_url_header'], null);

		if (is_null($url_string) || $url_string === '') {
			throw new AlexaVerificationException('Request signature verification failed: no URL specified.');
		}

		return new URL($url_string);
	}
}
