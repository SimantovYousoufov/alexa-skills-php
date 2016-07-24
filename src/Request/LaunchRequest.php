<?php

namespace AlexaPHP\Request;

class LaunchRequest // extends AlexaRequest  implements AlexaRequestInterface
{
	/**
	 * Request type
	 *
	 * @const string
	 */
	const REQUEST_TYPE = 'LaunchRequest';

	public function launchApplication()
	{
		// should be handled by the developer?
	}

	public function createAndStoreSession(array $data)
	{
		// ...
	}
}
