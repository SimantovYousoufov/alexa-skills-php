<?php

namespace AlexaPHP\Response;

use AlexaPHP\Card\CardInterface;
use AlexaPHP\Session\SessionInterface;

class Response implements ResponseInterface
{
	/**
	 * Session storage
	 *
	 * @var \AlexaPHP\Session\SessionInterface
	 */
	private $session;

	/**
	 * AlexaPHP config storage
	 *
	 * @var array
	 */
	private $config;

	/**
	 * Response constructor.
	 *
	 * @todo middleware can should the response
	 * @param \AlexaPHP\Session\SessionInterface $session
	 * @param array                              $config
	 */
	public function __construct(SessionInterface $session, array $config)
	{
		$this->session = $session;
		$this->config  = $config;
	}

	/**
	 * Return a speech response of either plaintext or SSML type
	 *
	 * @param string $text
	 * @param string $type
	 * @return array
	 */
	public function say($text, $type = self::TYPE_PLAINTTEXT)
	{
		return $this->respond(self::RESPONSE_TYPE_OUTPUT_SPEECH, $this->getSpeechResponse($text, $type));
	}

	/**
	 * Return a Card response
	 *
	 * @param \AlexaPHP\Card\CardInterface $card
	 * @return array
	 */
	public function card(CardInterface $card)
	{
		return $this->respond(self::RESPONSE_TYPE_CARD, $card->toArray());
	}

	/**
	 * Return a reprompt response, wrapping a speech response
	 *
	 * @param string $reprompt
	 * @param string $type
	 * @return array
	 */
	public function reprompt($reprompt, $type = self::TYPE_PLAINTTEXT)
	{
		return $this->respond(
			self::RESPONSE_TYPE_REPROMPT, [
				self::RESPONSE_TYPE_OUTPUT_SPEECH => $this->getSpeechResponse($reprompt, $type),
			]
		);
	}

	/**
	 * Is the session ending?
	 *
	 * @return bool
	 */
	public function isEnding()
	{
		return $this->session->expiring();
	}

	/**
	 * End the current session
	 *
	 * @return $this
	 */
	public function endSession()
	{
		$this->session->end();

		return $this;
	}

	/**
	 * Get the session
	 *
	 * @return \AlexaPHP\Session\SessionInterface
	 */
	public function getSession()
	{
		return $this->session;
	}

	/**
	 * Build a plaintext speech response
	 *
	 * @param string $text
	 * @param string $type
	 * @return array
	 */
	protected function getSpeechResponse($text, $type)
	{
		$response = [
			'type' => $type,
			'text' => $text,
		];

		if ($type === self::TYPE_SSML) {
			$response['ssml'] = $response['text'];

			unset($response['text']);
		}

		return $response;
	}

	/**
	 * Build a response in array form
	 *
	 * @param string $type
	 * @param array  $response
	 * @return array
	 */
	protected function respond($type, array $response)
	{
		return [
			'version'           => $this->config['application_version'],
			'sessionAttributes' => $this->session->getAttributes(),
			'response'          => [
				$type              => $response,
				'shouldEndSession' => $this->session->expiring(),
			],
		];
	}
}
