<?php

namespace AlexaPHP\Session;

use Carbon\Carbon;

class Session implements SessionInterface
{
	/**
	 * User data storage
	 *
	 * @var array
	 */
	protected $user;

	/**
	 * Is this a new session?
	 *
	 * @var bool|null
	 */
	protected $is_new_session;

	/**
	 * SessionInterface constructor.
	 *
	 * @param string $session_id
	 * @param array  $session_data
	 */
	public function __construct($session_id, array $session_data)
	{
		$this->setAttributes($session_data['attributes']); // @todo dole out data to setters accordingly
		$this->setUser($session_data['user']);

		$this->is_new_session = $session_data['new'];
	}

	/**
	 * Get a session for a session ID
	 *
	 * Will either create a new session or return an existing one.
	 *
	 * @param string $session_id
	 * @return static
	 */
	public static function getSessionForId($session_id)
	{
		// TODO: Implement getSessionForId() method.

		// if session doesn't exist, create and return a new static but don't save yet
	}

	/**
	 * Store the session
	 *
	 * @return bool
	 */
	public function save()
	{
		// TODO: Implement save() method.
	}

	/**
	 * Set expiration date for the session
	 *
	 * @param \Carbon\Carbon $expires
	 * @return static
	 */
	public function setExpiration(Carbon $expires)
	{
		// TODO: Implement setExpiration() method.
	}

	/**
	 * Get the expiration date for the session
	 *
	 * @return \Carbon\Carbon
	 */
	public function expires()
	{
		// TODO: Implement expires() method.
	}

	/**
	 * Is the session expired
	 *
	 * @return bool
	 */
	public function expired()
	{
		// TODO: Implement expired() method.
	}

	/**
	 * Force the session to expire
	 *
	 * @return bool
	 */
	public function end()
	{
		// TODO: Implement end() method.
	}

	/**
	 * Return User data for the session
	 *
	 * @return array
	 */
	public function user()
	{
		return $this->user;
	}

	/**
	 * Get all attributes for the session
	 *
	 * @return array
	 */
	public function attributes()
	{
		// TODO: Implement attributes() method.
	}

	/**
	 * Get an attribute by key
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getAttribute($key)
	{
		// TODO: Implement getAttribute() method.
	}

	/**
	 * Set an attribute by key
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @return static
	 */
	public function setAttribute($key, $value)
	{
		// TODO: Implement setAttribute() method.
	}

	/**
	 * Set many attributes
	 *
	 * @param array $attributes
	 * @return static
	 */
	public function setAttributes(array $attributes)
	{
		// TODO: Implement setAttributes() method.
	}

	/**
	 * Is this a new session
	 *
	 * @return bool
	 */
	public function isNew()
	{
		return $this->is_new_session;
	}

	/**
	 * Set user data
	 *
	 * @param array $user
	 * @return mixed
	 */
	public function setUser(array $user)
	{
		$this->user = $user;
	}
}
