<?php

namespace AlexaPHP\Request;

class LaunchRequest extends AlexaRequest implements AlexaRequestInterface
{
	/**
	 * Request type
	 *
	 * @const string
	 */
	const REQUEST_TYPE = 'LaunchRequest';

	/**
	 * Get the last action performed by the user
	 *
	 * @return null
	 */
	public function lastAction()
	{
		return null;
	}
}
