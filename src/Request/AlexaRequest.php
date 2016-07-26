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
	 * Get the last action performed by the user
	 */
	public function lastAction()
	{
		// @todo
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
}
