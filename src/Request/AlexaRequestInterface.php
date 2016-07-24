<?php

namespace AlexaPHP\Request;

use AlexaPHP\Persistence\CertificatePersistenceInterface;
use AlexaPHP\Session\SessionStorageInterface;
use Illuminate\Http\Request;

interface AlexaRequestInterface
{
	/**
	 * AlexaRequestInterface constructor.
	 *
	 * @param \Illuminate\Http\Request                              $request
	 * @param array                                                 $config
	 * @param \AlexaPHP\Persistence\CertificatePersistenceInterface $persistence
	 * @param \AlexaPHP\Session\SessionStorageInterface             $session_storage
	 */
	public function __construct(Request $request, array $config, CertificatePersistenceInterface $persistence, SessionStorageInterface $session_storage);

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
	public function requestType();
}
