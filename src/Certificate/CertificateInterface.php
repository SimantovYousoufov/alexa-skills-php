<?php

namespace AlexaPHP\Certificate;

interface CertificateInterface
{
	/**
	 * Certificate constructor.
	 *
	 * @param bool $from_location
	 * @param null $from_content
	 */
	public function __construct($from_location = false, $from_content = null);
	/**
	 * Create static from location
	 *
	 * @param string $location
	 * @return static
	 */
	public static function createFromLocation($location);

	/**
	 * Create static from string content
	 *
	 * @param string $data
	 * @return static
	 */
	public static function createFromString($data);

	/**
	 * Get the certificate's SANs
	 *
	 * @return string
	 */
	public function getSubjectAltNames();

	/**
	 * Determine whether the date specified on the certificate is valid
	 *
	 * @param bool $time
	 * @return bool
	 */
	public function hasValidDateConstraints($time = false);

	/**
	 * Get the certificate's public key
	 *
	 * @return resource
	 */
	public function publicKey();

	/**
	 * Verify the certificate and signature
	 *
	 * @param mixed  $data
	 * @param string $signature
	 * @param string $encryption_method
	 * @return bool
	 */
	public function verify($data, $signature, $encryption_method);
}
