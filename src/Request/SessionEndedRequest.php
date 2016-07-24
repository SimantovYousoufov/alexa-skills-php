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

	/**
	 * Reason storage
	 *
	 * @var string
	 */
	protected $reason;

	/**
	 * End the current session
	 *
	 * @return $this
	 */
	public function endSession()
	{
		// ...
		return $this;
	}

	/**
	 * Set the reason for ending this session
	 *
	 * @param string $reason
	 * @return $this
	 */
	public function setEndedReason($reason)
	{
		$this->reason = $reason;

		return $this;
	}

	/**
	 * Get the reason for ending this session
	 *
	 * @return string
	 */
	public function reason()
	{
		return $this->reason;
	}
}
