<?php

namespace AlexaPHP\Request;

use AlexaPHP\Exception\InvalidAlexaRequestException;
use AlexaPHP\Security\RequestVerifierInterface;
use AlexaPHP\Session\SessionInterface;
use Illuminate\Http\Request;

class RequestFactory
{
	/**
	 * Map request types to implementations
	 *
	 * @const array
	 */
	const REQUEST_TYPES = [
		LaunchRequest::REQUEST_TYPE       => LaunchRequest::class,
		IntentRequest::REQUEST_TYPE       => IntentRequest::class,
		SessionEndedRequest::REQUEST_TYPE => SessionEndedRequest::class,
	];

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
		$request_type = $request->get('request.type', null);

		if (is_null($request_type) || $request_type === '') {
			throw new InvalidAlexaRequestException('No request type specified.');
		}

		$request_class = self::REQUEST_TYPES[$request_type];

		return new $request_class($request, $config, $verifier, $session);
	}
}
