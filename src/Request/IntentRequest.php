<?php

namespace AlexaPHP\Request;

class IntentRequest extends AlexaRequest  implements AlexaRequestInterface
{
	/**
	 * Request type
	 *
	 * @const string
	 */
	const REQUEST_TYPE = 'IntentRequest';

	/**
	 * Get the current intent
	 */
	public function getIntent()
	{

	}

	/**
	 * Set the current intent
	 *
	 * @param string $intent
	 */
	public function setIntent($intent)
	{

	}

	/**
	 * Is this a launch request?
	 *
	 * @return bool
	 */
	public function isLaunchRequest()
	{
		// ...
	}

	public function launchApplication()
	{
		// should be handled by the developer?
	}

	/**
	 * Get slots for the request
	 *
	 * @return array
	 */
	public function getSlots()
	{
		return $this->get('request.intent.slots');
	}

	/**
	 * Get a specific slot
	 *
	 * @param string $slot
	 * @return mixed
	 */
	public function getSlot($slot)
	{
		return $this->get("request.intent.slots.$slot");
	}
}
