<?php

namespace AlexaPHP\Middleware;

use AlexaPHP\Certificate\Persistence\CertificatePersistenceInterface;
use AlexaPHP\Request\AlexaRequestInterface;
use AlexaPHP\Request\RequestFactory;
use Illuminate\Http\Request;

class AlexaRequestMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @return mixed
	 */
	public function handle(Request $request, \Closure $next)
	{
		$config = config('alexaphp');

		// @todo where should we catch exceptions to return responses?

		$persistence = $this->resolvePersistenceHandler($config);

		$session = $this->resolveSessionHandler($config, $request);

		$verifier = $this->resolveRequestVerifier($config, $request, $persistence);

		app()->instance(AlexaRequestInterface::class, RequestFactory::makeRequest($request, $verifier, $session, $config));

		return $next($request);
	}

	/**
	 * Get the persistence handler
	 *
	 * @param array $config
	 * @return \AlexaPHP\Certificate\Persistence\CertificatePersistenceInterface
	 */
	public function resolvePersistenceHandler(array $config)
	{
		$class = $config['certificate_persistence']['class'];

		return new $class($config['certificate_persistence']['config']); // @todo mkdir?
	}

	/**
	 * Get the session handler
	 *
	 * @param array                    $config
	 * @param \Illuminate\Http\Request $request
	 * @return \AlexaPHP\Session\SessionInterface
	 */
	public function resolveSessionHandler(array $config, Request $request)
	{
		$class = $config['session_handler'];

		return new $class($request->get('session.sessionId'), $request->get('session'));
	}

	/**
	 * Get the request verifier
	 *
	 * @param array                                                             $config
	 * @param \Illuminate\Http\Request                                          $request
	 * @param \AlexaPHP\Certificate\Persistence\CertificatePersistenceInterface $persistence
	 * @return \AlexaPHP\Security\RequestVerifierInterface
	 */
	public function resolveRequestVerifier(array $config, Request $request, CertificatePersistenceInterface $persistence)
	{
		$class = $config['request_verifier'];

		return new $class($request, $config, $persistence);
	}
}
