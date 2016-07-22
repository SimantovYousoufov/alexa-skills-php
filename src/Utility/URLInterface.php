<?php

namespace AlexaPHP\Utility;

/**
 * Class URL
 *
 * @package AlexaPHP\Utility
 */
interface URLInterface
{
	/**
	 * URL constructor.
	 *
	 * @param string $url
	 */
	public function __construct($url);

	/**
	 * Get or check scheme
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function scheme($check = false);

	/**
	 * Get or check host
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function host($check = false);

	/**
	 * Get or check port
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function port($check = false);

	/**
	 * Get or check user
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function user($check = false);

	/**
	 * Get or check pass
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function pass($check = false);

	/**
	 * Get or check path
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function path($check = false);

	/**
	 * Get or check query
	 *
	 * @param mixed $check
	 * @return array|bool
	 */
	public function query($check = false);

	/**
	 * Get or check fragment
	 *
	 * @param mixed $check
	 * @return bool
	 */
	public function fragment($check = false);

	/**
	 * Determine if the URL matches a given schema
	 *
	 * @param array $schema
	 * @return bool
	 */
	public function matchesSchema(array $schema);

	/**
	 * Get the parsed URL array
	 *
	 * @return array
	 */
	public function getParsedURL();

	/**
	 * Get the original URL
	 *
	 * @return string
	 */
	public function originalUrl();
}
