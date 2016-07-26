<?php

namespace AlexaPHP\Security;

use AlexaPHP\Certificate\CertificateInterface;
use AlexaPHP\Certificate\Persistence\CertificatePersistenceInterface;
use AlexaPHP\Utility\URLInterface;
use Illuminate\Http\Request;

interface RequestVerifierInterface
{
	/**
	 * RequestVerifier constructor.
	 *
	 * @param \Illuminate\Http\Request                                          $request
	 * @param array                                                             $config
	 * @param \AlexaPHP\Certificate\Persistence\CertificatePersistenceInterface $persistence
	 */
	public function __construct(Request $request, array $config, CertificatePersistenceInterface $persistence);

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
