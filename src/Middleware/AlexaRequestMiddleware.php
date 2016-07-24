<?php

namespace AlexaPHP\Middleware;

use AlexaPHP\Persistence\RemoteCertificatePersistence;
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
		$persistence = new RemoteCertificatePersistence(); // @todo

		app()->instance(AlexaRequestInterface::class, RequestFactory::makeRequest($request, $persistence));

		return $next($request);
	}
}
