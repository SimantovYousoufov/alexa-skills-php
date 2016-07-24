<?php

namespace AlexaPHP\Middleware;

use AlexaPHP\Persistence\RemoteCertificatePersistence;
use AlexaPHP\Request\AlexaRequestInterface;
use AlexaPHP\Request\RequestFactory;
use AlexaPHP\Session\Session;
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

		// @todo this should resolve the session and pass it to the factory
		$session = new Session($request->get('session.sessionId'), $request->get('session'));

		app()->instance(AlexaRequestInterface::class, RequestFactory::makeRequest($request, $persistence, $session));

		return $next($request);
	}
}
