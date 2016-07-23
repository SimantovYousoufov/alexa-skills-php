<?php

namespace AlexaPHP\Request;

use AlexaPHP\Persistence\CertificatePersistenceInterface;
use AlexaPHP\RequestVerifier;
use Illuminate\Http\Request;

abstract class AlexaRequest implements AlexaRequestInterface
{
	private $verifier;

	public function __construct(Request $request, array $config, CertificatePersistenceInterface $persistence)
	{
		$this->verifier = new RequestVerifier($request, $config, $persistence);
		$this->verifier->verifyRequest();
	}
}
