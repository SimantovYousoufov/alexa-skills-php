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
