<?php

namespace AlexaPHP\Request;

use AlexaPHP\Persistence\CertificatePersistenceInterface;
use Illuminate\Http\Request;

interface AlexaRequestInterface
{
	/**
	 * AlexaRequestInterface constructor.
	 *
	 * @param \Illuminate\Http\Request                              $request
	 * @param array                                                 $config
	 * @param \AlexaPHP\Persistence\CertificatePersistenceInterface $persistence
	 */
	public function __construct(Request $request, array $config, CertificatePersistenceInterface $persistence);

	/**
	 * Get the session from storage
	 *
	 * @param string $session_id
	 * @return \AlexaPHP\Session\SessionStorageInterface
	 */
	public function getSessionFromStorage($session_id);

	/**
	 * Return the current request type
	 *
	 * @return string
	 */
	public static function requestType();
}
