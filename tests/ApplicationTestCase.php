<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Request\IntentRequest;
use AlexaPHP\Request\LaunchRequest;
use AlexaPHP\Request\SessionEndedRequest;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class ApplicationTestCase extends OrchestraTestCase
{
	/**
	 * Get base path.
	 *
	 * @return string
	 */
	protected function getBasePath()
	{
		// reset base path to point to our package's src directory
		return __DIR__ . '/../vendor/orchestra/testbench/fixture';
	}

	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application $app
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{
		// Setup default database to use sqlite :memory:
		$app['config']->set(
			'alexaphp', [
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
				'request_handlers'              => [
					LaunchRequest::REQUEST_TYPE       => LaunchRequest::class,
					IntentRequest::REQUEST_TYPE       => IntentRequest::class,
					SessionEndedRequest::REQUEST_TYPE => SessionEndedRequest::class,
				],
			]
		);
	}
}
