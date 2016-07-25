<?php

namespace AlexaPHP\Request;

class IntentRequest extends AlexaRequest implements AlexaRequestInterface
{
	/**
	 * Request type
	 *
	 * @const string
	 */
	const REQUEST_TYPE = 'IntentRequest';

	/**
	 * Current intent
	 *
	 * @var string
	 */
	protected $intent;

	/**
	 * Is this a new session
	 *
	 * @var bool|null
	 */
	protected $is_new_session;

	/**
	 * Get the current intent
	 */
	public function getIntent()
	{
		if (is_null($this->intent)) {
			$this->setIntent($this->request->get('request.intent.name'));
		}

		return $this->intent;
	}

	/**
	 * Set the current intent
	 *
	 * @param string $intent
	 * @return $this
	 */
	public function setIntent($intent)
	{
		$this->intent = $intent;

		return $this;
	}

	/**
	 * Is this a launch request?
	 *
	 * @return bool
	 */
	public function isLaunchRequest()
	{
		return $this->session->isNew();
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
