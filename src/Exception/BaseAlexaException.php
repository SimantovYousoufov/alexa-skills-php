<?php

namespace AlexaPHP\Exception;

use RuntimeException;

class BaseAlexaException extends RuntimeException
{
	/**
	 * VerificationException constructor.
	 *
	 * @param string     $message
	 */
	public function __construct($message)
	{
		parent::__construct($message);
	}
}
