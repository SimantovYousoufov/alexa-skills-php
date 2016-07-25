<?php

namespace AlexaPHP\Session;

use Carbon\Carbon;

interface SessionInterface
{
	/**
	 * SessionInterface constructor.
	 *
	 * @param string $session_id
	 * @param array  $session_data
	 */
	public function __construct($session_id, array $session_data);

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
	 * Is the session expiring?
	 *
	 * Will the response end the session by setting the 'shouldEndSession' flag
	 *
	 * @return bool
	 */
	public function expiring();

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
	public function getAttributes();

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
	 * Set user data
	 *
	 * @param array $user
	 * @return mixed
	 */
	public function setUser(array $user);

	/**
	 * Is this a new session
	 *
	 * @return bool
	 */
	public function isNew();
}
