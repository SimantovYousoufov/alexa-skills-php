<?php

namespace AlexaPHP\Middleware;

use AlexaPHP\Certificate\Persistence\RemoteCertificatePersistence;
use AlexaPHP\Request\AlexaRequestInterface;
use AlexaPHP\Request\RequestFactory;
use AlexaPHP\Security\RequestVerifier;
use AlexaPHP\Session\EphemeralSession;
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
		$persistence = new RemoteCertificatePersistence(); // @todo

		// @todo: get and pass config here?
		$config = [];

		// @todo this should resolve the session and pass it to the factory
		$session = new EphemeralSession($request->get('session.sessionId'), $request->get('session'));

		$verifier = new RequestVerifier($request, $config, $persistence); // @todo where should the verifier verifyRequest get called?

		app()->instance(AlexaRequestInterface::class, RequestFactory::makeRequest($request, $verifier, $session, $config));

		return $next($request);
	}
}
