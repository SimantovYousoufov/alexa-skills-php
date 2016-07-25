<?php

namespace AlexaPHP\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\ServiceProvider;

class AlexaServiceProvider extends ServiceProvider
{

	/**
	 * Register any other events for your application.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher $events
	 * @return void
	 */
	public function boot(DispatcherContract $events)
	{
		parent::boot($events);

		$this->publishes(
			[
				__DIR__ . '../config/alexaphp.php' => config_path('alexaphp.php'),
			]
		);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// pass
	}
}
