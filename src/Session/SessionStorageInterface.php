<?php

namespace AlexaPHP\Session;

use Carbon\Carbon;

interface SessionStorageInterface
{
	/**
	 * SessionStorageInterface constructor.
	 *
	 * @param string $session_id
	 */
	public function __construct($session_id);

	/**
	 * Get a session for a session ID
	 *
	 * Will either create a new session or return an existing one.
	 *
	 * @param string $session_id
	 * @return static
	 */
	public static function getSessionForId($session_id);

	/**
	 * Store the session
	 *
	 * @return bool
	 */
	public function save();

	/**
	 * Set expiration date for the session
	 *
	 * @param \Carbon\Carbon $expires
	 * @return static
	 */
	public function setExpiration(Carbon $expires);

	/**
	 * Get the expiration date for the session
	 *
	 * @return \Carbon\Carbon
	 */
	public function expires();

	/**
	 * Is the session expired
	 *
	 * @return bool
	 */
	public function expired();

	/**
	 * Force the session to expire
	 *
	 * @return bool
	 */
	public function end();

	/**
	 * Return User data for the session
	 *
	 * @return array
	 */
	public function user();

	/**
	 * Get all attributes for the session
	 *
	 * @return array
	 */
	public function attributes();

	/**
	 * Get an attribute by key
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getAttribute($key);

	/**
	 * Set an attribute by key
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @return static
	 */
	public function setAttribute($key, $value);

	/**
	 * Set many attributes
	 *
	 * @param array $attributes
	 * @return static
	 */
	public function setAttributes(array $attributes);

	/**
	 * Is this a new session
	 *
	 * @return bool
	 */
	public function isNew();
}
