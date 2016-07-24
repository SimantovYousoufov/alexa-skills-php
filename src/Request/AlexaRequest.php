<?php

namespace AlexaPHP\Request;

use AlexaPHP\Persistence\CertificatePersistenceInterface;
use AlexaPHP\RequestVerifier;
use AlexaPHP\Session\SessionStorageInterface;
use Illuminate\Http\Request;

abstract class AlexaRequest implements AlexaRequestInterface
{
	/**
	 * Request type
	 *
	 * @const string|null
	 */
	const REQUEST_TYPE = null;

	/**
	 * RequestVerifier storage
	 *
	 * @var \AlexaPHP\RequestVerifier
	 */
	protected $verifier;

	/**
	 * SessionStorage container
	 *
	 * @var \AlexaPHP\Session\SessionStorageInterface
	 */
	protected $session_storage;

	/**
	 * Input storage
	 *
	 * @var array
	 */
	private $input;

	/**
	 * End the session on response
	 *
	 * @var bool
	 */
	protected $end_session = false;

	/**
	 * AlexaRequest constructor.
	 *
	 * @param \Illuminate\Http\Request                              $request
	 * @param array                                                 $config
	 * @param \AlexaPHP\Persistence\CertificatePersistenceInterface $persistence
	 * @param \AlexaPHP\Session\SessionStorageInterface             $session_storage
	 */
	public function __construct(Request $request, array $config, CertificatePersistenceInterface $persistence, SessionStorageInterface $session_storage)
	{
		$this->verifier = new RequestVerifier($request, $config, $persistence);
		$this->verifier->verifyRequest();
		$this->session_storage = $session_storage;
		$this->setInput($request->all());
	}

	/**
	 * Set this requests input
	 *
	 * @param array $input
	 * @return $this
	 */
	protected function setInput(array $input)
	{
		$this->input = $input;

		return $this;
	}

	/**
	 * Get a key from the input
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		return array_get($this->input, $key);
	}

	/**
	 * Return an Alexa response
	 *
	 * @param array $response
	 */
	public function respond(array $response)
	{
		// Send a response to Alexa

		// If end the session, attach the flag
	}

	/**
	 * Return a response with an audio file
	 *
	 * @param string $file
	 */
	public function respondWithAudio($file)
	{
		// ...
	}

	/**
	 * Say something
	 * @param string $say
	 */
	public function say($say)
	{
		// ...
	}

	/**
	 * Ask the user something, keep session open
	 *
	 * @param string $ask
	 */
	public function ask($ask)
	{
		// ...
	}

	/**
	 * Tell the user something, close session
	 *
	 * @param string $tell
	 */
	public function tell($tell)
	{
		// ...
	}

	/**
	 * Get the last action performed by the user
	 */
	public function lastAction()
	{

	}

	/**
	 * Set the flag to expire the session
	 *
	 * @param bool $end_session
	 */
	public function setEndSession($end_session = true)
	{
		$this->end_session = $end_session;
	}

	/**
	 * Retrieve the session from storage
	 *
	 * @param string $session_id
	 * @return static
	 */
	public function getSessionFromStorage($session_id)
	{
		return $this->session_storage->getSessionForId($session_id);
	}

	/**
	 * Get the request type
	 *
	 * @return string|null
	 */
	public function requestType()
	{
		return static::REQUEST_TYPE;
	}

	/**
	 * Create and store a new session
	 *
	 * @param string $session_id
	 * @param array  $data
	 */
	public function createAndStoreSession($session_id, array $data)
	{
		$session = $this->session_storage->getSessionForId($session_id);
		$session->setAttributes($data);
		$session->save();
	}
}
