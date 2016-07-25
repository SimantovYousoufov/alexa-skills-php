<?php

use AlexaPHP\Request\IntentRequest;
use AlexaPHP\Request\LaunchRequest;
use AlexaPHP\Security\RequestVerifier;
use AlexaPHP\Session\EphemeralSession;
use AlexaPHP\Request\SessionEndedRequest;
use AlexaPHP\Certificate\Persistence\LocalFileCertificatePersistence;

return [
	/*
	|--------------------------------------------------------------------------
	| Header Configuration
	|--------------------------------------------------------------------------
	|
	| Header names for Alexa request data
	|
    */
	'cert_chain_url_header' => env('ALEXAPHP_CERT_CHAIN_URL_HEADER', 'SignatureCertChainUrl'),

	'signature_header'              => env('ALEXAPHP_SIGNATURE_HEADER', 'Signature'),

	/*
	|--------------------------------------------------------------------------
	| Alexa Request Configuration
	|--------------------------------------------------------------------------
	|
	| Configuration for different aspects of an Alexa request
	|
    */
	'timestamp_delay_limit_seconds' => env('ALEXAPHP_TIMESTAMP_DELAY_LIMIT_SECONDS', 150),

	'expect_scheme' => env('ALEXAPHP_EXPECT_SCHEME', 'https'),

	'expect_path_start_regexp' => env('ALEXAPHP_EXPECT_PATH_START_REGEXP', '/^\/echo.api/'),

	'expect_port' => env('ALEXAPHP_EXPECT_PORT', 443),

	'expect_host' => env('ALEXAPHP_EXPECT_HOST', 's3.amazonaws.com'),

	'expect_san' => env('ALEXAPHP_EXPECT_SAN', 'echo-api.amazon.com'),

	'encryption_method' => env('ALEXAPHP_ENCRYPTION_METHOD', 'sha1WithRSAEncryption'),

	'application_id'   => env('ALEXAPHP_APPLICATION_ID', 'arbitrary'),

	/*
	|--------------------------------------------------------------------------
	| Handlers
	|--------------------------------------------------------------------------
	|
	| Configuration for Alexa request lifecycle handlers
	|
    */
	'request_handlers' => [
		LaunchRequest::REQUEST_TYPE       => LaunchRequest::class,
		IntentRequest::REQUEST_TYPE       => IntentRequest::class,
		SessionEndedRequest::REQUEST_TYPE => SessionEndedRequest::class,
	],

	'session_handler' => EphemeralSession::class,

	'certificate_persistence' => [
		'class'  => LocalFileCertificatePersistence::class,
		'config' => [
			'storage_dir' => storage_path('alexaphp'),
		],
	],

	'request_verifier' => RequestVerifier::class,
];
