<?php

namespace AlexaPHP\Security;

use AlexaPHP\Certificate\CertificateInterface;
use AlexaPHP\Utility\URLInterface;

interface RequestVerifierInterface
{
	/**
	 * Verify the Alexa request
	 *
	 * @return void
	 */
	public function verifyRequest();

	/**
	 * Verify that the certificate URL is valid
	 *
	 * @param \AlexaPHP\Utility\URLInterface $url
	 * @return bool
	 */
	public function verifySignatureCertificateUrl(URLInterface $url);

	/**
	 * Verify that we're working with a valid certificate
	 *
	 * @param \AlexaPHP\Certificate\CertificateInterface $certificate
	 * @return bool
	 */
	public function verifyCertificate(CertificateInterface $certificate);

	/**
	 * Verify the timestamp on the request
	 *
	 * @return bool
	 */
	public function verifyTimestamp();

	/**
	 * Verify that the request is for our application
	 *
	 * @return bool
	 */
	public function verifyApplicationId();
}
