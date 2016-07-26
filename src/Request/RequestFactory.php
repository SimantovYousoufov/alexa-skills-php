<?php

namespace AlexaPHP\Request;

use AlexaPHP\Exception\InvalidAlexaRequestException;
use AlexaPHP\Security\RequestVerifierInterface;
use AlexaPHP\Session\SessionInterface;
use Illuminate\Http\Request;

class RequestFactory
{
	/**
	 * Create an Alexa Request object
	 *
	 * @param \Illuminate\Http\Request                    $request
	 * @param \AlexaPHP\Security\RequestVerifierInterface $verifier
	 * @param \AlexaPHP\Session\SessionInterface          $session
	 * @param array                                       $config
	 * @return \AlexaPHP\Request\AlexaRequestInterface
	 */
	public static function makeRequest(Request $request, RequestVerifierInterface $verifier, SessionInterface $session, array $config)
	{
		$request_type = $request->input('request.type', null);

		if (is_null($request_type) || $request_type === '' || ! isset($config['request_handlers'][$request_type])) {
			throw new InvalidAlexaRequestException('Invalid request type specified.');
		}

		$request_class = $config['request_handlers'][$request_type];

		return new $request_class($request, $config, $verifier, $session);
	}
}
