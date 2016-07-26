<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Card\Card;
use AlexaPHP\Response\Response;
use AlexaPHP\Session\EphemeralSession;
use Mockery;

class AlexaResponseTest extends ApplicationTestCase
{
	public function testItAcceptsSession()
	{
		$session = Mockery::mock(EphemeralSession::class);

		$response = new Response($session, config('alexaphp'));

		$this->assertEquals($session, $response->getSession());
	}

	public function testItDefaultsToNotExpireSession()
	{
		$session = Mockery::mock(EphemeralSession::class);

		$response = new Response($session, config('alexaphp'));

		$session->shouldReceive('expiring')->andReturn(false);
		$this->assertFalse($response->isEnding());
	}

	public function testItSetsAndGetsEndSession()
	{
		$session = Mockery::mock(EphemeralSession::class);

		$response = new Response($session, config('alexaphp'));

		$session->shouldReceive('end')->andReturn($session);
		$response->endSession();

		$session->shouldReceive('expiring')->andReturn(true);
		$this->assertTrue($response->isEnding());
	}

	public function testItReturnsSpeechResponseOfPlaintextType()
	{
		$session = Mockery::mock(EphemeralSession::class);
		$session->shouldReceive('getAttributes')->andReturn(['some' => 'attribute']);
		$session->shouldReceive('expiring')->andReturn(false);

		$response = new Response($session, config('alexaphp'));

		$text = 'Some plaintext response.';
		$output = $response->speech($text);

		$this->assertEquals([
			'version' => config('alexaphp.application_version'),
			'sessionAttributes' => ['some' => 'attribute'],
			'response' => [
				'outputSpeech' => [
					'type' => 'PlainText',
					'text' => $text,
				],
				'shouldEndSession' => false,
			],
		], $output);
	}

	public function testItReturnsSpeechResponseOfSSMLType()
	{
		$session = Mockery::mock(EphemeralSession::class);
		$session->shouldReceive('getAttributes')->andReturn(['some' => 'attribute']);
		$session->shouldReceive('expiring')->andReturn(false);

		$response = new Response($session, config('alexaphp'));

		$text = 'Some plaintext response.';
		$output = $response->speech($text, Response::TYPE_SSML);

		$this->assertEquals([
			'version' => config('alexaphp.application_version'),
			'sessionAttributes' => ['some' => 'attribute'],
			'response' => [
				'outputSpeech' => [
					'type' => 'SSML',
					'ssml' => $text,
				],
				'shouldEndSession' => false,
			],
		], $output);
	}

	public function testItReturnsCardResponse()
	{
		$session = Mockery::mock(EphemeralSession::class);
		$session->shouldReceive('getAttributes')->andReturn(['some' => 'attribute']);
		$session->shouldReceive('expiring')->andReturn(false);

		$card_data = [
			'type' => 'Simple',
			'title' => 'SomeTitle',
			'content' => 'SomeContent',
			'text' => 'SomeText',
			'image' => [
				'smallImageUrl' => 'smallURL',
				'largeImageUrl' => 'largeURL',
			],
		];
		$card = Mockery::mock(Card::class);
		$card->shouldReceive('toArray')->andReturn($card_data);

		$response = new Response($session, config('alexaphp'));

		$output = $response->card($card);

		$this->assertEquals([
			'version' => config('alexaphp.application_version'),
			'sessionAttributes' => ['some' => 'attribute'],
			'response' => [
				'card' => $card_data,
				'shouldEndSession' => false,
			],
		], $output);
	}

	public function testItReturnsRepromptResponseTypeOfPlaintextType()
	{
		$session = Mockery::mock(EphemeralSession::class);
		$session->shouldReceive('getAttributes')->andReturn(['some' => 'attribute']);
		$session->shouldReceive('expiring')->andReturn(false);

		$response = new Response($session, config('alexaphp'));

		$text = 'Some plaintext response.';
		$output = $response->reprompt($text);

		$this->assertEquals([
			'version' => config('alexaphp.application_version'),
			'sessionAttributes' => ['some' => 'attribute'],
			'response' => [
				'reprompt' => [
					'outputSpeech' => [
						'type' => 'PlainText',
						'text' => $text,
					],
				],
				'shouldEndSession' => false,
			],
		], $output);
	}

	public function testItReturnsRepromptResponseTypeOfSSMLType()
	{
		$session = Mockery::mock(EphemeralSession::class);
		$session->shouldReceive('getAttributes')->andReturn(['some' => 'attribute']);
		$session->shouldReceive('expiring')->andReturn(false);

		$response = new Response($session, config('alexaphp'));

		$text = 'Some plaintext response.';
		$output = $response->reprompt($text, Response::TYPE_SSML);

		$this->assertEquals([
			'version' => config('alexaphp.application_version'),
			'sessionAttributes' => ['some' => 'attribute'],
			'response' => [
				'reprompt' => [
					'outputSpeech' => [
						'type' => 'SSML',
						'ssml' => $text,
					],
				],
				'shouldEndSession' => false,
			],
		], $output);
	}
}
