<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Request\AlexaRequest;
use AlexaPHP\Security\RequestVerifier;
use AlexaPHP\Session\EphemeralSession;
use Illuminate\Http\Request;
use Mockery;

class AlexaRequestFactoryTest extends TestCase
{
	public $config = [
		'cert_chain_url_header'         => 'SignatureCertChainUrl',
		'signature_header'              => 'Signature',
		'timestamp_delay_limit_seconds' => 150,
		'expect_scheme'                 => 'https',
		'expect_path_start_regexp'      => '/^\/echo.api/',
		'expect_port'                   => 443,
		'expect_host'                   => 's3.amazonaws.com',
		'expect_san'                    => 'echo-api.amazon.com',
		'encryption_method'             => 'sha1WithRSAEncryption',
		'application_id'                => 'arbitrary',
	];


}
