<?php

namespace AlexaPHP\Certificate\Persistence;

use AlexaPHP\Certificate\Certificate;
use AlexaPHP\Certificate\CertificateInterface;
use Carbon\Carbon;

class RemoteCertificatePersistence implements CertificatePersistenceInterface
{
	/**
	 * Get the certificate for a given URL
	 *
	 * @param string $url
	 * @return \AlexaPHP\Certificate\CertificateInterface
	 */
	public function getCertificateForURL($url)
	{
		// Certificate can pull from external location
		return new Certificate($url);
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
		// pass
	}

	/**
	 * Force expiration for a given URL
	 *
	 * @param string $url
	 * @return bool
	 */
	public function expireCertificateForURL($url)
	{
		// pass
	}
}
