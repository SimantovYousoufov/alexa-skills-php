<?php

namespace AlexaPHP\Request;

use AlexaPHP\Security\RequestVerifierInterface;
use AlexaPHP\Session\SessionInterface;
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
	 * Output Speech response type
	 *
	 * @const string
	 */
	const RESPONSE_OUTPUT_SPEECH = 'outputSpeech';

	/**
	 * Card response type
	 *
	 * @const string
	 */
	const RESPONSE_CARD = 'card';

	/**
	 * Reprompt response type
	 *
	 * @const string
	 */
	const RESPONSE_REPROMPT = 'reprompt';

	/**
	 * RequestVerifier storage
	 *
	 * @var \AlexaPHP\Security\RequestVerifier
	 */
	protected $verifier;

	/**
	 * SessionStorage container
	 *
	 * @var \AlexaPHP\Session\SessionInterface
	 */
	protected $session;

	/**
	 * Request storage
	 *
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * Configuration storage
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * Input storage
	 *
	 * @var array
	 */
	private $input;

	/**
	 * Response container
	 *
	 * @var array
	 */
	protected $response;

	/**
	 * AlexaRequest constructor.
	 *
	 * @param \Illuminate\Http\Request                    $request
	 * @param array                                       $config
	 * @param \AlexaPHP\Security\RequestVerifierInterface $verifier
	 * @param \AlexaPHP\Session\SessionInterface          session
	 */
	public function __construct(Request $request, array $config, RequestVerifierInterface $verifier, SessionInterface $session)
	{
		$this->verifier = $verifier;
		$this->verifier->verifyRequest();

		$this->request = $request;
		$this->session = $session;
		$this->config  = $config;

		$this->setInput($request->all());
	}

	/**
	 * Getter for Illuminate Request
	 *
	 * @return \Illuminate\Http\Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Getter for request verifier
	 *
	 * @return \AlexaPHP\Security\RequestVerifier
	 */
	public function getVerifier()
	{
		return $this->verifier;
	}

	/**
	 * Getter for request input
	 *
	 * @return array
	 */
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * Getter for configuration
	 *
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * Getter for Alexa Session
	 *
	 * @return \AlexaPHP\Session\SessionInterface
	 */
	public function getSession()
	{
		return $this->session;
	}

	/**
	 * Set this requests input
	 *
	 * @param array $input
	 * @return $this
	 */
	public function setInput(array $input)
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
	 *
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
	 * @return array
	 */
	public function ask($ask)
	{
		$speech = $this->getPlaintextSpeechResponse($ask);

		return $this->buildResponse($speech);
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
		// ...
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
	 * Build a plaintext speech response
	 *
	 * @param string $text
	 * @return array
	 */
	public function getPlaintextSpeechResponse($text)
	{
		return [
			'type'     => self::RESPONSE_OUTPUT_SPEECH,
			'response' => [
				'type' => 'PlainText',
				'text' => $text,
			],
		];
	}

	/**
	 * Build a response in array form
	 *
	 * @param array $response
	 * @return array
	 */
	public function buildResponse(array $response)
	{
		return [
			'version'           => '1.0',
			"sessionAttributes" => $this->session->getAttributes(),
			"response"          => [
				$response['type']  => $response['response'],
				"shouldEndSession" => $this->session->expiring(),
			],
		];
	}
}
