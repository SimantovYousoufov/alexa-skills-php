<?php

namespace AlexaPHP\Request;

use AlexaPHP\Persistence\CertificatePersistenceInterface;
use AlexaPHP\RequestVerifier;
use AlexaPHP\Session\SessionStorageInterface;
use Illuminate\Http\Request;

abstract class AlexaRequest implements AlexaRequestInterface
{
	const REQUEST_TYPE = null;

	private $verifier;
	private $session_storage;

	public function __construct(Request $request, array $config, CertificatePersistenceInterface $persistence, SessionStorageInterface $session_storage)
	{
		$this->verifier = new RequestVerifier($request, $config, $persistence);
		$this->verifier->verifyRequest();
		$this->session_storage = $session_storage;
	}

	public function respond(array $response)
	{
		// Send a response to Alexa

		// If end the session, attach the flag
	}

	public function respondWithAudio($file)
	{
		// ...
	}

	public function say($say)
	{
		// ...
	}

	public function ask($ask)
	{
		// ...
	}

	public function tell($tell)
	{
		// ...
	}

	public function setEndSession($end_session = true)
	{
		// Set the flag to end the session
	}

	public function getSessionFromStorage($session_id)
	{
		return $this->session_storage->getSessionForId($session_id);
	}

	public function requestType()
	{
		return static::REQUEST_TYPE;
	}
}
