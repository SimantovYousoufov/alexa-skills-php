<?php

namespace AlexaPHP\Card;

use InvalidArgumentException;

class Card implements CardInterface
{
	/**
	 * Card attribute storage
	 *
	 * @var array
	 */
	protected $attributes = [
		'type' => null,
		'title' => null,
		'content' => null,
		'text' => null,
		'image' => [
			'smallImageUrl' => null,
			'largeImageUrl' => null,
		]
	];

	/**
	 * Card constructor.
	 *
	 * @param array $attributes
	 */
	public function __construct(array $attributes = [])
	{
		foreach ($attributes as $key => $value) {
			$this->set($key, $value);
		}
	}

	/**
	 * Set the card's type
	 *
	 * @param string $type
	 * @return mixed
	 */
	public function setType($type = self::SIMPLE)
	{
		$this->set('type', $type);

		return $this;
	}

	/**
	 * Get card type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Set card title
	 *
	 * @param string $title
	 * @return string
	 */
	public function setTitle($title)
	{
		$this->set('title', $title);

		return $this;
	}

	/**
	 * Get card title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set card content
	 *
	 * @param string $content
	 * @return string
	 */
	public function setContent($content)
	{
		$this->set('content', $content);

		return $this;
	}

	/**
	 * Get card content
	 *
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Set card text
	 *
	 * @param string $text
	 * @return string
	 */
	public function setText($text)
	{
		$this->set('text', $text);

		return $this;
	}

	/**
	 * Get card text
	 *
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

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
	public function setImage(array $image)
	{
		$this->set('image', $image);

		return $this;
	}

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
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * Serialize card to array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->attributes;
	}

	/**
	 * Set the key's value
	 *
	 * @param string $key
	 * @param string|array $value
	 */
	private function set($key, $value)
	{
		if (! array_key_exists($key, $this->attributes)) {
			$class = self::class;
			throw new InvalidArgumentException("$key is not a valid attribute of $class.");
		}

		$this->attributes[$key] = $value;
	}

	/**
	 * Get a card's property
	 *
	 * @param $name
	 * @return array|string
	 */
	public function __get($name)
	{
		if (! array_key_exists($name, $this->attributes)) {
			$class = self::class;
			throw new InvalidArgumentException("$name is not a valid attribute of $class.");
		}

		return $this->attributes[$name];
	}
}
