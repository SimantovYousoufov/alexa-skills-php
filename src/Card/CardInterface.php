<?php

namespace AlexaPHP\Card;

interface CardInterface
{
	/**
	 * Simple card type
	 *
	 * @const string
	 */
	const SIMPLE = 'Simple';

	/**
	 * Standard card type
	 *
	 * @const string
	 */
	const STANDARD = 'Standard';

	/**
	 * LinkAccount card type
	 *
	 * @const string
	 */
	const LINK_ACCOUNT = 'LinkAccount';

	/**
	 * Set the card's type
	 *
	 * @param string $type
	 * @return mixed
	 */
	public function setType($type = self::SIMPLE);

	/**
	 * Get card type
	 *
	 * @return string
	 */
	public function getType();

	/**
	 * Set card title
	 *
	 * @param string $title
	 * @return string
	 */
	public function setTitle($title);

	/**
	 * Get card title
	 *
	 * @return string
	 */
	public function getTitle();

	/**
	 * Set card content
	 *
	 * @param string $content
	 * @return string
	 */
	public function setContent($content);

	/**
	 * Get card content
	 *
	 * @return string
	 */
	public function getContent();

	/**
	 * Set card text
	 *
	 * @param string $text
	 * @return string
	 */
	public function setText($text);

	/**
	 * Get card text
	 *
	 * @return string
	 */
	public function getText();

	/**
	 * Set card image
	 *
	 * Array = [
	 *     'smallImageUrl' => '',
	 *     'largeImageUrl' => '',
	 * ]
	 *
	 * @param array $image
	 * @return static
	 */
	public function setImage(array $image);

	/**
	 * Get card image array
	 *
	 * Array = [
	 *     'smallImageUrl' => '',
	 *     'largeImageUrl' => '',
	 * ]
	 *
	 * @return array
	 */
	public function getImage();

	/**
	 * Serialize card to array
	 *
	 * @return array
	 */
	public function toArray();
}
