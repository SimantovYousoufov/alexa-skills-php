<?php

namespace AlexaPHP\Request;

class SessionEndedRequest // extends AlexaRequest  implements AlexaRequestInterface
{
	/**
	 * Request type
	 *
	 * @const string
	 */
	const REQUEST_TYPE = 'SessionEndedRequest';

	public function endSession()
	{
		// ...
	}

	public function reason()
	{
		// why did the session end?
	}
}
