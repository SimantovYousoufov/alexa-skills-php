<?php

namespace AlexaPHP\Persistence;

use AlexaPHP\Certificate\CertificateInterface;
use Carbon\Carbon;

interface CertificatePersistenceInterface
{
	/**
	 * Get the certificate for a given URL
	 *
	 * @param string $url
	 * @return \AlexaPHP\Certificate\CertificateInterface
	 */
	public function getCertificateForURL($url);

	/**
	 * Store a certificate for a given URL
	 *
	 * @param string                                     $url
	 * @param \AlexaPHP\Certificate\CertificateInterface $certificate
	 * @param \Carbon\Carbon                             $expiration_date
	 * @return bool
	 */
	public function storeCertificateForURL($url, CertificateInterface $certificate, Carbon $expiration_date);

	/**
	 * Force expiration for a given URL
	 *
	 * @param string $url
	 * @return bool
	 */
	public function expireCertificateForURL($url);
}
