<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Certificate\CertificateInterface;
use AlexaPHP\Certificate\Persistence\LocalFileCertificatePersistence;
use AlexaPHP\Middleware\AlexaRequestMiddleware;
use AlexaPHP\Request\AlexaRequestInterface;
use AlexaPHP\Security\RequestVerifier;
use AlexaPHP\Security\RequestVerifierInterface;
use AlexaPHP\Session\EphemeralSession;
use AlexaPHP\Utility\URLInterface;
use Illuminate\Http\Request;
use Mockery;

class AlexaMiddlewareTest extends ApplicationTestCase
{
	const TEMP_DIR = __DIR__ . '/' . 'temp';

	/**
	 * Start with a cleaned temp dir
	 */
	public function setUp()
	{
		if (is_dir(self::TEMP_DIR)) {
			$this->cleanDir(self::TEMP_DIR);
		}

		return parent::setUp(); // TODO: Change the autogenerated stub
	}

	/**
	 * End with a cleaned temp dir
	 */
	public function tearDown()
	{
		parent::tearDown(); // TODO: Change the autogenerated stub

		if (is_dir(self::TEMP_DIR)) {
			$this->cleanDir(self::TEMP_DIR);
		}
	}

	// http://stackoverflow.com/questions/7288029/php-delete-directory-that-is-not-empty
	public function cleanDir($dir) {
		foreach(scandir($dir) as $file) {
			if ('.' === $file || '..' === $file) {
				continue;
			}

			if (is_dir("$dir/$file")) {
				$this->cleanDir("$dir/$file");
			} else {
				unlink("$dir/$file");
			}
		}

		rmdir($dir);
	}

	public function testItResolvesPersistence()
	{
		$middleware = new AlexaRequestMiddleware;

		$persistence = $middleware->resolvePersistenceHandler(config('alexaphp'));

		$this->assertTrue($persistence instanceof LocalFileCertificatePersistence);
	}

	public function testItResolvesSession()
	{
		$middleware = new AlexaRequestMiddleware;

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('get')->with('session.sessionId')->andReturn('someId');
		$request->shouldReceive('get')->with('session')->andReturn(
			[
				'new'        => false,
				'attributes' => [
					'foo' => 'bar',
				],
				'user'       => [
					'userId' => 45,
				],
			]
		);

		$session = $middleware->resolveSessionHandler(config('alexaphp'), $request);

		$this->assertTrue($session instanceof EphemeralSession);
	}

	public function testItResolvesRequestVerifier()
	{
		$middleware = new AlexaRequestMiddleware;

		$request     = Mockery::mock(Request::class);
		$persistence = Mockery::mock(LocalFileCertificatePersistence::class);

		$request_verifier = $middleware->resolveRequestVerifier(config('alexaphp'), $request, $persistence);

		$this->assertTrue($request_verifier instanceof RequestVerifier);
	}

	public function testItHandlesRequestAndPassesToNext()
	{
		$this->app['config']->set('alexaphp.request_verifier', VerifierStub::class);
		$this->app['config']->set('alexaphp.request_handlers.IntentRequest', IntentRequestStub::class);

		$middleware = new AlexaRequestMiddleware;
		$next = function (Request $request) {
			return 'success!';
		};

		$request = Mockery::mock(Request::class);
		$request->shouldReceive('get')->with('session.sessionId')->andReturn('someId');
		$request->shouldReceive('get')->with('session')->andReturn(
			[
				'new'        => false,
				'attributes' => [
					'foo' => 'bar',
				],
				'user'       => [
					'userId' => 45,
				],
			]
		);
		$request->shouldReceive('get')->with('request.type', NULL)->andReturn('IntentRequest');

		$return = $middleware->handle($request, $next);

		$this->assertEquals('success!', $return);
	}
}

class VerifierStub implements RequestVerifierInterface
{
	/**
	 * Verify the Alexa request
	 *
	 * @return void
	 */
	public function verifyRequest()
	{
		// TODO: Implement verifyRequest() method.
	}

	/**
	 * Verify that the certificate URL is valid
	 *
	 * @param \AlexaPHP\Utility\URLInterface $url
	 * @return bool
	 */
	public function verifySignatureCertificateUrl(URLInterface $url)
	{
		return true;
	}

	/**
	 * Verify that we're working with a valid certificate
	 *
	 * @param \AlexaPHP\Certificate\CertificateInterface $certificate
	 * @return bool
	 */
	public function verifyCertificate(CertificateInterface $certificate)
	{
		return true;
	}

	/**
	 * Verify the timestamp on the request
	 *
	 * @return bool
	 */
	public function verifyTimestamp()
	{
		return true;
	}

	/**
	 * Verify that the request is for our application
	 *
	 * @return bool
	 */
	public function verifyApplicationId()
	{
		return true;
	}
}

class IntentRequestStub implements AlexaRequestInterface
{
	/**
	 * AlexaRequestInterface constructor.
	 *
	 * @param \Illuminate\Http\Request                    $request
	 * @param array                                       $config
	 * @param \AlexaPHP\Security\RequestVerifierInterface $verifier
	 * @param \AlexaPHP\Session\SessionInterface          $session_storage
	 */
	public function __construct(\Illuminate\Http\Request $request, array $config, \AlexaPHP\Security\RequestVerifierInterface $verifier, \AlexaPHP\Session\SessionInterface $session_storage)
	{

	}

	/**
	 * Return the current request type
	 *
	 * @return string
	 */
	public function requestType()
	{
		// TODO: Implement requestType() method.
	}

	/**
	 * Return an Alexa response
	 *
	 * @param array $response
	 */
	public function respond(array $response)
	{
		// TODO: Implement respond() method.
	}

	/**
	 * Return a response with an audio file
	 *
	 * @param string $file
	 */
	public function respondWithAudio($file)
	{
		// TODO: Implement respondWithAudio() method.
	}

	/**
	 * Say something
	 *
	 * @param string $say
	 */
	public function say($say)
	{
		// TODO: Implement say() method.
	}

	/**
	 * Ask the user something, keep session open
	 *
	 * @param string $ask
	 */
	public function ask($ask)
	{
		// TODO: Implement ask() method.
	}

	/**
	 * Tell the user something, close session
	 *
	 * @param string $tell
	 */
	public function tell($tell)
	{
		// TODO: Implement tell() method.
	}

	/**
	 * Get the last action performed by the user
	 */
	public function lastAction()
	{
		// TODO: Implement lastAction() method.
	}

	/**
	 * Getter for Alexa Session
	 *
	 * @return \AlexaPHP\Session\SessionInterface
	 */
	public function getSession()
	{
		// TODO: Implement getSession() method.
	}

	/**
	 * Getter for request verifier
	 *
	 * @return \AlexaPHP\Security\RequestVerifier
	 */
	public function getVerifier()
	{
		// TODO: Implement getVerifier() method.
	}
}
