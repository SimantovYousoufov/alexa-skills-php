<?php

namespace AlexaPHP\Utility;

/**
 * Class URL
 *
 * @package AlexaPHP\Utility
 */
class URL
{
	/**
	 * Parsed URL storage
	 *
	 * @var array|bool
	 */
	private $url;

	private $url_string;

	private $supported_keys = ['scheme', 'host', 'path', 'port', 'user', 'pass', 'query', 'fragment'];

	/**
	 * URL constructor.
	 *
	 * @param string $url
	 */
	public function __construct($url)
	{
		$this->url_string = $url;
		$this->url = parse_url($url);

		if ($this->url === false) {
			throw new \InvalidArgumentException('Invalid URL passed to ' . self::class);
		}
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
		if (! in_array($key, $this->supported_keys)) {
			throw new \InvalidArgumentException('Invalid key specified.');
		}

		if ($check) {
			return $this->url[$key] === $check;
		}

		return isset($this->url[$key]) ? $this->url[$key] : null;
	}

	/**
	 * Get or check scheme
	 *
	 * @param bool $check
	 * @return bool
	 */
	public function scheme($check = false)
	{
		return $this->getOrCheckKey('scheme', $check);
	}

	/**
	 * Get or check host
	 *
	 * @param bool $check
	 * @return bool
	 */
	public function host($check = false)
	{
		return $this->getOrCheckKey('host', $check);
	}

	/**
	 * Get or check port
	 *
	 * @param bool $check
	 * @return bool
	 */
	public function port($check = false)
	{
		return $this->getOrCheckKey('port', $check);
	}

	/**
	 * Get or check user
	 *
	 * @param bool $check
	 * @return bool
	 */
	public function user($check = false)
	{
		return $this->getOrCheckKey('user', $check);
	}

	/**
	 * Get or check pass
	 *
	 * @param bool $check
	 * @return bool
	 */
	public function pass($check = false)
	{
		return $this->getOrCheckKey('pass', $check);
	}

	/**
	 * Get or check path
	 *
	 * @param bool $check
	 * @return bool
	 */
	public function path($check = false)
	{
		return $this->getOrCheckKey('path', $check);
	}

	/**
	 * Get or check query
	 *
	 * @param bool $check
	 * @return bool
	 */
	public function query($check = false)
	{
		return $this->getOrCheckKey('query', $check);
	}

	/**
	 * Get or check fragment
	 *
	 * @param bool $check
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
}
