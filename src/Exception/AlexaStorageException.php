<?php

namespace AlexaPHP\Exception;

class AlexaStorageException extends BaseAlexaException
{
	/**
	 * VerificationException constructor.
	 *
	 * @param string $message
	 */
	public function __construct($message)
	{
		parent::__construct($message);
	}
}
