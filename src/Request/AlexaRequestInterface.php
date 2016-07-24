<?php

namespace AlexaPHP\Request;

use AlexaPHP\Persistence\CertificatePersistenceInterface;
use AlexaPHP\Session\SessionInterface;
use Illuminate\Http\Request;

interface AlexaRequestInterface
{
	/**
	 * AlexaRequestInterface constructor.
	 *
	 * @param \Illuminate\Http\Request                              $request
	 * @param array                                                 $config
	 * @param \AlexaPHP\Persistence\CertificatePersistenceInterface $persistence
	 * @param \AlexaPHP\Session\SessionInterface             $session_storage
	 */
	public function __construct(Request $request, array $config, CertificatePersistenceInterface $persistence, SessionInterface $session_storage);

	/**
	 * Get the session from storage
	 *
	 * @param string $session_id
	 * @return \AlexaPHP\Session\SessionInterface
	 */
	public function getSessionFromStorage($session_id);

	/**
	 * Return the current request type
	 *
	 * @return string
	 */
	public function requestType();
}
