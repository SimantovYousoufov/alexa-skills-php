<?php

namespace AlexaPHP;

use Illuminate\Http\Request;

class AlexaSkillRequestHandler
{
	/**
	 * Request container
	 *
	 * @var \Illuminate\Http\Request
	 */
	private $request;

	/**
	 * Verifier container
	 *
	 * @var \AlexaPHP\RequestVerifier
	 */
	private $verifier;

	public function __construct(Request $request)
	{
		$this->request = $request;

		$this->verifier = new RequestVerifier($request);
		$this->verifier->verifyRequest();
	}
}
