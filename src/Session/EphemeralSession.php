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
		$attributes = isset($session_data['attributes']) ? $session_data['attributes'] : [];
		$user = isset($session_data['user']) ? $session_data['user'] : [];

		$this->setAttributes($attributes);
		$this->setUser($user);

		$this->is_new_session = $session_data['new'];
	}

	/**
	 * Get a session for a session ID
	 *
	 * Will either create a new session or return an existing one.
	 *
	 * @param string $session_id
	 * @param null   $session_data
	 * @return static
	 */
	public static function getSessionForId($session_id, $session_data = null)
	{
		return new static($session_id, $session_data);
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
	 * Set this session as new
	 *
	 * @todo how to handle logic for this in the constructor?
	 *
	 * @return static
	 */
	public function setIsNew()
	{
		$this->is_new_session = true;

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
