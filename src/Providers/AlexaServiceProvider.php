<?php

namespace AlexaPHP\Providers;

use AlexaPHP\Middleware\AlexaRequestMiddleware;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\ServiceProvider;

class AlexaServiceProvider extends ServiceProvider
{
	/**
	 * Register bindings in the container.
	 *
	 * @return void
	 */
	public function register()
	{

	}

	/**
	 * Register any other events for your application.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher  $events
	 * @return void
	 */
	public function boot(DispatcherContract $events)
	{
		parent::boot($events);

		$this->publishes([
			__DIR__.'../config/alexaphp.php' => config_path('alexaphp.php'),
		]);
	}
}
