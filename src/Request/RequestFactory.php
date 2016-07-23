<?php

namespace AlexaPHP\Request;

use AlexaPHP\Exception\InvalidAlexaRequestException;
use AlexaPHP\Persistence\CertificatePersistenceInterface;
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
	 * @return \AlexaPHP\Request\AlexaRequestInterface
	 */
	public static function makeRequest(Request $request, CertificatePersistenceInterface $persistence)
	{
		$request_type = $request->get('request.type', null);

		if (is_null($request_type) || $request_type === '') {
			throw new InvalidAlexaRequestException('No request type specified.');
		}

		$config = []; // @todo get config

		$request_class = self::REQUEST_TYPES[$request_type];

		return new $request_class($request, $config, $persistence);
	}
}
