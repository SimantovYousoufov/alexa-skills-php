<?php

namespace AlexaPHP\Request;

use LogicException;

class SessionEndedRequest extends AlexaRequest implements AlexaRequestInterface
{
	/**
	 * Request type
	 *
	 * @const string
	 */
	const REQUEST_TYPE = 'SessionEndedRequest';

	/**
	 * Session ended because user said 'exit'
	 *
	 * @const string
	 */
	const REASON_USER_EXIT = 'REASON_USER_EXIT';

	/**
	 * Session ended because user was idle
	 *
	 * @const string
	 */
	const REASON_USER_IDLE = 'REASON_USER_IDLE';

	/**
	 * Session ended because of an error
	 *
	 * @todo  handle error cases
	 *
	 * @const string
	 */
	const REASON_ERROR = 'REASON_ERROR';

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
