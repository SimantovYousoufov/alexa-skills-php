Alexa Skills PHP Library [![Build Status](https://img.shields.io/travis/SimantovYousoufov/alexa-skills-php/master.svg?style=flat)](https://travis-ci.org/SimantovYousoufov/alexa-skills-php)
============================================================================================================================================================================================
Utility package to handle the tedium of setting up a custom web service for Alexa skills.

## Setup
1. Require the package `composer require syousoufov/alexa-skills-php`  
2. Attach the service provider to `config/app.php`:
  
    ```php
        // ..etc
        /*
         * Third Party Service Providers
         */
        AlexaPHP\Providers\AlexaServiceProvider::class,
        // ..etc
    ```

3. Publish the `alexa-skills-php` config with `php artisan vendor:publish --provider="AlexaPHP\Providers\AlexaServiceProvider"` and change the required values. The defaults for everything except `application_id` should work just fine. 
4. Add the route middleware to the `$routeMiddleware` stack in `app/Http/Kernel.php`:
  
    ```php
        /**
         * The application's route middleware.
         *
         * @var array
         */
        protected $routeMiddleware = [
            'alexa'    => \AlexaPHP\Middleware\AlexaRequestMiddleware::class,
        ];
    ```
5. Routes can now be served through the middleware:  

    ```php
    $router->group(
        ['middleware' => ['alexa'],], function (Router $router) {
            $router->post('alexa', 'AlexaSkillsController@handleAlexaRequest');
    });
    ```

## Handling requests and returning responses
Alexa requests can be routed to a single controller method which can act as a funnel to pass the request to appropriate handlers.

```php
<?php

namespace App\Http\Controllers;

use AlexaPHP\Request\AlexaRequestInterface;
use AlexaPHP\Request\IntentRequest;
use AlexaPHP\Request\LaunchRequest;
use AlexaPHP\Request\SessionEndedRequest;
use AlexaPHP\Response\ResponseInterface;

class AlexaSkillsController extends Controller
{
    /**
     * Handle Alexa requests
     *
     * @param \AlexaPHP\Request\AlexaRequestInterface $alexa_request
     * @param \AlexaPHP\Response\ResponseInterface    $response
     * @return array
     */
    public function handleAlexaRequest(AlexaRequestInterface $alexa_request, ResponseInterface $response)
    {
        // Pass to its appropriate handler
        $method = camel_case($alexa_request->requestType());
    
        return $this->$method($alexa_request, $response);
    }

    /**
     * Handle a launch request
     *
     * @param \AlexaPHP\Request\LaunchRequest      $alexa_request
     * @param \AlexaPHP\Response\ResponseInterface $response
     * @return array
     */
    public function launchRequest(LaunchRequest $alexa_request, ResponseInterface $response)
    {
        return $response->say('LaunchRequest handled.');
    }

    /**
     * Handle an intent request
     *
     * @param \AlexaPHP\Request\IntentRequest      $alexa_request
     * @param \AlexaPHP\Response\ResponseInterface $response
     * @return array
     */
    public function intentRequest(IntentRequest $alexa_request, ResponseInterface $response)
    {
        $intent = $alexa_request->getIntent();

        return $response->say("$intent handled.");
    }

    /**
     * Handle a session end request
     *
     * @param \AlexaPHP\Request\SessionEndedRequest $alexa_request
     * @param \AlexaPHP\Response\ResponseInterface  $response
     * @return array
     */
    public function sessionEndedRequest(SessionEndedRequest $alexa_request, ResponseInterface $response)
    {
        return $response->endSession()->say('SessionEndedRequest handled.');
    }
}
```

### Response types
#### [Speech Response](https://developer.amazon.com/public/solutions/alexa/alexa-skills-kit/docs/alexa-skills-kit-interface-reference#outputspeech-object) - return some speech to render to the user

```php
    /**
     * Handle an intent request
     *
     * @param \AlexaPHP\Request\IntentRequest      $alexa_request
     * @param \AlexaPHP\Response\ResponseInterface $response
     * @return array
     */
    public function intentRequest(IntentRequest $alexa_request, ResponseInterface $response)
    {
        $intent = $alexa_request->getIntent();

        return $response->say("$intent handled.");
    }
```

#### [Card](https://developer.amazon.com/public/solutions/alexa/alexa-skills-kit/docs/alexa-skills-kit-interface-reference#card-object) - return a card to render to the Amazon Alexa app

```php
	/**
	 * Handle an intent request
	 *
	 * @param \AlexaPHP\Request\IntentRequest      $alexa_request
	 * @param \AlexaPHP\Response\ResponseInterface $response
	 * @return array
	 */
	public function intentRequest(IntentRequest $alexa_request, ResponseInterface $response)
	{
		$intent = $alexa_request->getIntent();

		$card = new Card([
			'type' => 'Simple',
			'title' => 'SomeTitle',
			'content' => "Handled $intent!",
			'text' => 'SomeText',
			'image' => [
				'smallImageUrl' => 'http://someimg.com/url.jpg',
				'largeImageUrl' => 'http://someimg.com/urlx2.jpg',
			],
		]);

		$card->content; // Handled TestIntent!

		return $response->card($card);
	}
```

#### [Reprompt](https://developer.amazon.com/public/solutions/alexa/alexa-skills-kit/docs/alexa-skills-kit-interface-reference#reprompt-object) - issue a reprompt

```php
	/**
	 * Handle an intent request
	 *
	 * @param \AlexaPHP\Request\IntentRequest      $alexa_request
	 * @param \AlexaPHP\Response\ResponseInterface $response
	 * @return array
	 */
	public function intentRequest(IntentRequest $alexa_request, ResponseInterface $response)
	{
		$intent = $alexa_request->getIntent();

		return $response->reprompt("$intent handled.");
	}
```

#### Output Types
You can also specify PlainText or SSML speech output types for `say` and `reprompt`:

```php
	/**
	 * Handle an intent request
	 *
	 * @param \AlexaPHP\Request\IntentRequest      $alexa_request
	 * @param \AlexaPHP\Response\ResponseInterface $response
	 * @return array
	 */
	public function intentRequest(IntentRequest $alexa_request, ResponseInterface $response)
	{
		return $response->reprompt("<speak>This output speech uses SSML.</speak>", ResponseInterface::TYPE_SSML);
	}
```

## Extracting data from the request
This is not an exhaustive list of all available methods so please review the source to get a better understanding of what's available while this readme gets filled out.

```php
	/**
	 * Handle Alexa requests
	 *
	 * @param \AlexaPHP\Request\AlexaRequestInterface $alexa_request
	 * @param \AlexaPHP\Response\ResponseInterface    $response
	 * @return array
	 */
	public function handleAlexaRequest(AlexaRequestInterface $alexa_request, ResponseInterface $response)
	{
		$request_type = $alexa_request->requestType();

		$session             = $alexa_request->getSession();
		$session_will_expire = $session->expiring();
		$user                = $session->user();
		$attributes          = $session->getAttribute('some.attribute');
		
		$verifier = $alexa_request->getVerifier();
		$lets_check_again = $verifier->verifyTimestamp();
		
		if (! $lets_check_again) {
			throw new AccessDeniedHttpException("Naughty, naughty");
		}

		return $response->say('I did stuff!');
	}
```

## Testing
1. Run `composer install` then `phpunit`.
1. Code coverage: `phpunit --coverage-html tests/coverage/ && open tests/coverage/index.html`

## TODO
1. Complete documentation
1. Open source the example web service
1. Re-evaluate API decisions to get a more fluent syntax
1. Get from 96% code coverage to 100%

