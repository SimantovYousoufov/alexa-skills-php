<?php

namespace AlexaPHP\Utility;

/**
 * Class URL
 *
 * @package AlexaPHP\Utility
 */
class URL implements URLInterface
{
	/**
	 * Parsed URL storage
	 *
	 * @var array|bool
	 */
	private $parsed_url;

	/**
	 * Container for the actual URL
	 *
	 * @var string
	 */
	private $url_string;

	/**
	 * URL constructor.
	 *
	 * @param string $url
	 */
	public function __construct($url)
	{
		$this->url_string = $url;
		$this->parsed_url = parse_url($url);

		if ($this->parsed_url === false) {
			throw new \InvalidArgumentException('Malformed URL passed to ' . self::class);
		}
	}

	/**
	 * Get or check scheme
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function scheme($check = false)
	{
		return $this->getOrCheckKey('scheme', $check);
	}

	/**
	 * Get or check host
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function host($check = false)
	{
		return $this->getOrCheckKey('host', $check);
	}

	/**
	 * Get or check port
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function port($check = false)
	{
		return $this->getOrCheckKey('port', $check);
	}

	/**
	 * Get or check user
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function user($check = false)
	{
		return $this->getOrCheckKey('user', $check);
	}

	/**
	 * Get or check pass
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function pass($check = false)
	{
		return $this->getOrCheckKey('pass', $check);
	}

	/**
	 * Get or check path
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function path($check = false)
	{
		return $this->getOrCheckKey('path', $check);
	}

	/**
	 * Get or check query
	 *
	 * @param mixed $check
	 * @return array|bool
	 */
	public function query($check = false)
	{
		if ($check) {
			$query = [];
			parse_str($this->getOrCheckKey('query', false), $query);

			return $query === $check;
		}

		return $this->getOrCheckKey('query', $check);
	}

	/**
	 * Get or check fragment
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function fragment($check = false)
	{
		return $this->getOrCheckKey('fragment', $check);
	}

	/**
	 * Determine if the URL matches a given schema
	 *
	 * @param array $schema
	 * @return bool
	 */
	public function matchesSchema(array $schema)
	{
		foreach ($schema as $key => $value) {
			$matches = $this->$key($value);

			if (! $matches) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the parsed URL array
	 *
	 * @return array
	 */
	public function getParsedURL()
	{
		return $this->parsed_url;
	}

	/**
	 * Get the original URL
	 *
	 * @return string
	 */
	public function originalUrl()
	{
		return $this->url_string;
	}

	/**
	 * Get or check a key's value
	 *
	 * @param string $key
	 * @param bool|string $check
	 * @return mixed
	 */
	private function getOrCheckKey($key, $check)
	{
		if ($check) {
			return $this->parsed_url[$key] === $check;
		}

		return isset($this->parsed_url[$key]) ? $this->parsed_url[$key] : null;
	}
}
