<?php

namespace AlexaPHP\Response;

use AlexaPHP\Card\CardInterface;
use AlexaPHP\Session\SessionInterface;

interface ResponseInterface
{
	/**
	 * Output Speech response type
	 *
	 * @const string
	 */
	const RESPONSE_TYPE_OUTPUT_SPEECH = 'outputSpeech';

	/**
	 * Card response type
	 *
	 * @const string
	 */
	const RESPONSE_TYPE_CARD = 'card';

	/**
	 * Reprompt response type
	 *
	 * @const string
	 */
	const RESPONSE_TYPE_REPROMPT = 'reprompt';

	/**
	 * PlainText speech response type
	 *
	 * @const string
	 */
	const TYPE_PLAINTTEXT = 'PlainText';

	/**
	 * SSML speech response type
	 *
	 * @const string
	 */
	const TYPE_SSML = 'SSML';

	/**
	 * Response constructor.
	 *
	 * @param \AlexaPHP\Session\SessionInterface $session
	 * @param array                              $config
	 */
	public function __construct(SessionInterface $session, array $config);

	/**
	 * Return a speech response of either plaintext or SSML type
	 *
	 * @param string $text
	 * @param string $type
	 * @return array
	 */
	public function say($text, $type = self::TYPE_PLAINTTEXT);

	/**
	 * Return a Card response
	 *
	 * @param \AlexaPHP\Card\CardInterface $card
	 * @return array
	 */
	public function card(CardInterface $card);

	/**
	 * Return a reprompt response, wrapping a speech response
	 *
	 * @param string $reprompt
	 * @param string $type
	 * @return array
	 */
	public function reprompt($reprompt, $type = self::TYPE_PLAINTTEXT);

	/**
	 * Is the session ending?
	 *
	 * @return bool
	 */
	public function isEnding();

	/**
	 * End the current session
	 *
	 * @return $this
	 */
	public function endSession();
}
