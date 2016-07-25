<?php

namespace AlexaPHP\Request;

use AlexaPHP\Security\RequestVerifierInterface;
use AlexaPHP\Session\SessionInterface;
use Illuminate\Http\Request;

interface AlexaRequestInterface
{
	/**
	 * AlexaRequestInterface constructor.
	 *
	 * @param \Illuminate\Http\Request                    $request
	 * @param array                                       $config
	 * @param \AlexaPHP\Security\RequestVerifierInterface $verifier
	 * @param \AlexaPHP\Session\SessionInterface          $session_storage
	 */
	public function __construct(Request $request, array $config, RequestVerifierInterface $verifier, SessionInterface $session_storage);

	/**
	 * Return the current request type
	 *
	 * @return string
	 */
	public function requestType();

	/**
	 * Return an Alexa response
	 *
	 * @param array $response
	 */
	public function respond(array $response);

	/**
	 * Return a response with an audio file
	 *
	 * @param string $file
	 */
	public function respondWithAudio($file);

	/**
	 * Say something
	 *
	 * @param string $say
	 */
	public function say($say);

	/**
	 * Ask the user something, keep session open
	 *
	 * @param string $ask
	 */
	public function ask($ask);

	/**
	 * Tell the user something, close session
	 *
	 * @param string $tell
	 */
	public function tell($tell);

	/**
	 * Get the last action performed by the user
	 */
	public function lastAction();

	/**
	 * Getter for Alexa Session
	 *
	 * @return \AlexaPHP\Session\SessionInterface
	 */
	public function getSession();

	/**
	 * Getter for request verifier
	 *
	 * @return \AlexaPHP\Security\RequestVerifier
	 */
	public function getVerifier();
}
