<?php

namespace AlexaPHP\Certificate\Persistence;

use AlexaPHP\Certificate\CertificateInterface;

interface CertificatePersistenceInterface
{
	/**
	 * Get the certificate for a given Key
	 *
	 * @param string $key
	 * @return \AlexaPHP\Certificate\CertificateInterface|bool
	 */
	public function getCertificateForKey($key);

	/**
	 * Store a certificate for a given Key
	 *
	 * @param string                                     $key
	 * @param \AlexaPHP\Certificate\CertificateInterface $certificate
	 * @return bool
	 */
	public function storeCertificateForKey($key, CertificateInterface $certificate);

	/**
	 * Force expiration for a given Key
	 *
	 * @param string $key
	 * @return bool
	 */
	public function expireCertificateForKey($key);
}
