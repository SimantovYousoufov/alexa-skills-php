<?php

namespace AlexaPHP\Request;

use AlexaPHP\Exception\InvalidAlexaRequestException;
use AlexaPHP\Persistence\CertificatePersistenceInterface;
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
	 * @param \Illuminate\Http\Request                              $request
	 * @param \AlexaPHP\Persistence\CertificatePersistenceInterface $persistence
	 * @param \AlexaPHP\Session\SessionInterface                    $session
	 * @return \AlexaPHP\Request\AlexaRequestInterface
	 */
	public static function makeRequest(Request $request, CertificatePersistenceInterface $persistence, SessionInterface $session)
	{
		// @todo this function should be called by some middleware which binds the AlexaRequest object to the container
		// which can then be resolved in the controller and sent to the appropriate method(s)
		$request_type = $request->get('request.type', null);

		if (is_null($request_type) || $request_type === '') {
			throw new InvalidAlexaRequestException('No request type specified.');
		}

		$config = []; // @todo get config

		$request_class = self::REQUEST_TYPES[$request_type];

		return new $request_class($request, $config, $persistence, $session);
	}
}
