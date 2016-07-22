<?php

namespace AlexaPHP\Exception;

class AlexaCertificateException extends BaseAlexaException
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
