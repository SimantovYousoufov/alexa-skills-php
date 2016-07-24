<?php

namespace AlexaPHP\Request;

class IntentRequest // extends AlexaRequest  implements AlexaRequestInterface
{
	/**
	 * Request type
	 *
	 * @const string
	 */
	const REQUEST_TYPE = 'IntentRequest';

	public function getIntent()
	{

	}

	public function isLaunchRequest()
	{
		// ...
	}

	public function launchApplication()
	{
		// should be handled by the developer?
	}

	public function createAndStoreSession(array $data)
	{
		// ...
	}

	/**
	 * Get slots for the request
	 *
	 * @return array
	 */
	public function getSlots()
	{

	}

	/**
	 * Get a specific slot
	 *
	 * @param string $slot
	 * @return mixed
	 */
	public function getSlot($slot)
	{
		
	}
}
