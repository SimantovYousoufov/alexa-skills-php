<?php

namespace AlexaPHP\Session;

class EphemeralSession implements SessionInterface
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
	 * Session attributes
	 *
	 * @var array
	 */
	protected $attributes;

	/**
	 * Should this session end
	 *
	 * @var bool
	 */
	protected $should_end_session = false;

	/**
	 * SessionInterface constructor.
	 *
	 * @param string $session_id
	 * @param array  $session_data
	 */
	public function __construct($session_id, array $session_data)
	{
		$this->setAttributes($session_data['attributes']);
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
	 * Is the session expiring?
	 *
	 * Will the response end the session by setting the 'shouldEndSession' flag
	 *
	 * @return bool
	 */
	public function expiring()
	{
		return $this->should_end_session;
	}

	/**
	 * Force the session to expire
	 *
	 * @return bool
	 */
	public function end()
	{
		$this->should_end_session = true;

		return $this;
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
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * Get an attribute by key
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getAttribute($key)
	{
		return array_get($this->attributes, $key);
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
		array_set($this->attributes, $key, $value);

		return $this;
	}

	/**
	 * Set many attributes
	 *
	 * @param array $attributes
	 * @return static
	 */
	public function setAttributes(array $attributes)
	{
		$this->attributes = $attributes;

		return $this;
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
