<?php

namespace AlexaPHP\Certificate\Persistence;

use AlexaPHP\Certificate\CertificateInterface;
use Carbon\Carbon;

class LocalFileCertificatePersistence implements CertificatePersistenceInterface
{

	/**
	 * Get the certificate for a given URL
	 *
	 * @param string $url
	 * @return \AlexaPHP\Certificate\CertificateInterface
	 */
	public function getCertificateForURL($url)
	{
		// TODO: Implement getCertificateForURL() method.
	}

	/**
	 * Store a certificate for a given URL
	 *
	 * @param string                                     $url
	 * @param \AlexaPHP\Certificate\CertificateInterface $certificate
	 * @param \Carbon\Carbon                             $expiration_date
	 * @return bool
	 */
	public function storeCertificateForURL($url, CertificateInterface $certificate, Carbon $expiration_date)
	{
		// TODO: Implement storeCertificateForURL() method.
	}

	/**
	 * Force expiration for a given URL
	 *
	 * @param string $url
	 * @return bool
	 */
	public function expireCertificateForURL($url)
	{
		// TODO: Implement expireCertificateForURL() method.
	}
}
