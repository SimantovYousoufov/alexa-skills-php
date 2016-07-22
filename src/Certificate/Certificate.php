<?php

namespace AlexaPHP\Certificate;

use Carbon\Carbon;
use ErrorException;
use AlexaPHP\Exception\AlexaCertificateException;

class Certificate implements CertificateInterface
{
	/**
	 * Container for OpenSSL Certificate Resource
	 *
	 * @var resource
	 */
	private $certificate_resource;

	/**
	 * Parsed certificate container
	 *
	 * @var array
	 */
	private $parsed_certificate;

	/**
	 * Contents of cert file
	 *
	 * @var string
	 */
	private $contents;

	/**
	 * Certificate constructor.
	 *
	 * @param bool $from_location
	 * @param bool $from_content
	 */
	public function __construct($from_location = false, $from_content = false)
	{
		if (! $from_location && ! $from_content) {
			throw new AlexaCertificateException('No valid location or data for certificate specified.');
		}

		$this->contents           = $from_location ? $this->retrieveCertificateFromLocation($from_location) :
			$from_content;
		$this->parsed_certificate = $this->parse($this->contents);
	}

	/**
	 * Create static from location
	 *
	 * @param string $location
	 * @return static
	 */
	public static function createFromLocation($location)
	{
		return new static($location);
	}

	/**
	 * Create static from string content
	 *
	 * @param string $data
	 * @return static
	 */
	public static function createFromString($data)
	{
		return new static(false, $data);
	}

	/**
	 * Get the certificate's SANs
	 *
	 * @return string
	 */
	public function getSubjectAltNames()
	{
		return $this->parsed_certificate['extensions']['subjectAltName'];
	}

	/**
	 * Determine whether the date specified on the certificate is valid
	 *
	 * @param bool $time
	 * @return bool
	 */
	public function hasValidDateConstraints($time = false)
	{
		$from = $this->getStartDate(false);
		$to   = $this->getEndDate(false);

		$now = $time ?: time();

		return ($from <= $now) && ($now <= $to);
	}

	/**
	 * Get the certificate's public key
	 *
	 * @return resource
	 */
	public function publicKey()
	{
		return $this->getDetails()['key'];
	}

	/**
	 * Get details for the certificate
	 *
	 * @param null $data
	 * @return array
	 */
	public function getDetails($data = null)
	{
		$data = is_null($data) ? $this->contents : $data;

		try {
			$details = openssl_pkey_get_details(openssl_pkey_get_public($data));
		} catch (\ErrorException $e) {
			throw new AlexaCertificateException('Unable to get details for certificate.');
		}

		// Unless all PHP errors are turned into exceptions (i.e. in Laravel via set_error_handler(),
		// we won't get \ErrorExceptions thrown so we should try to catch failures this way as well.
		if (! $details) {
			throw new AlexaCertificateException('Unable to get details for certificate.');
		}

		return $details;
	}

	/**
	 * Get the certificate's start date
	 *
	 * @param bool $carbon
	 * @return \Carbon|Carbon|int
	 */
	public function getStartDate($carbon = true)
	{
		return $carbon ? Carbon::createFromTimestamp($this->parsed_certificate['validFrom_time_t']) :
			$this->parsed_certificate['validFrom_time_t'];
	}

	/**
	 * Get the certificate's end date
	 *
	 * @param bool $carbon
	 * @return \Carbon|Carbon|int
	 */
	public function getEndDate($carbon = true)
	{
		return $carbon ? Carbon::createFromTimestamp($this->parsed_certificate['validTo_time_t']) :
			$this->parsed_certificate['validTo_time_t'];
	}

	/**
	 * Verify the certificate and signature
	 *
	 * @param mixed  $data
	 * @param string $signature
	 * @param string $encryption_method
	 * @return bool
	 */
	public function verify($data, $signature, $encryption_method)
	{
		return openssl_verify($data, $signature, $this->publicKey(), $encryption_method) === 1;
	}

	/**
	 * Getter for parsed certificate
	 *
	 * @return array
	 */
	public function getParsedCertificate()
	{
		return $this->parsed_certificate;
	}

	/**
	 * Parse a certificate
	 *
	 * @param string $certificate
	 * @return array
	 */
	private function parse($certificate)
	{
		try {
			$this->certificate_resource = openssl_x509_read($certificate);
		} catch (ErrorException $e) {
			throw new AlexaCertificateException('Unable to read certificate data.');
		}

		// Unless all PHP errors are turned into exceptions (i.e. in Laravel via set_error_handler(),
		// we won't get \ErrorExceptions thrown so we should try to catch failures this way as well.
		if (! $this->certificate_resource) {
			throw new AlexaCertificateException('Unable to read certificate data.');
		}

		return openssl_x509_parse($this->certificate_resource);
	}

	/**
	 * Retrieve the certificate for a given URL
	 *
	 * @todo should implement some form of persistence
	 *
	 * @param string $location
	 * @return string
	 */
	private function retrieveCertificateFromLocation($location)
	{
		$data = file_get_contents($location);

		if (is_null($data) || $data === '') {
			throw new AlexaCertificateException('Unable to retrieve certificate.');
		}

		return $data;
	}
}
