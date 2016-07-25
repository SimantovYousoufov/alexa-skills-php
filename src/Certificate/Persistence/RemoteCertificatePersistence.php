<?php

namespace AlexaPHP\Certificate\Persistence;

use AlexaPHP\Certificate\Certificate;
use AlexaPHP\Certificate\CertificateInterface;
use Carbon\Carbon;

class RemoteCertificatePersistence implements CertificatePersistenceInterface
{
	/**
	 * Get the certificate for a given Key
	 *
	 * @param string $key
	 * @return \AlexaPHP\Certificate\CertificateInterface
	 */
	public function getCertificateForKey($key)
	{
		// Certificate can pull from external location
		return new Certificate($key);
	}

	/**
	 * Store a certificate for a given Key
	 *
	 * @param string                                     $key
	 * @param \AlexaPHP\Certificate\CertificateInterface $certificate
	 * @return bool
	 */
	public function storeCertificateForKey($key, CertificateInterface $certificate)
	{
		return false;
	}

	/**
	 * Force expiration for a given Key
	 *
	 * @param string $key
	 * @return bool
	 */
	public function expireCertificateForKey($key)
	{
		return false;
	}
}
