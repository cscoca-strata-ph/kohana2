<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Provides a driver-based interface for finding, creating, and deleting cached
 * resources. Caches are identified by a unique string. Tagging of caches is
 * also supported, and caches can be found and deleted by id or tag.
 *
 * $Id: Cache.php 4605 2009-09-14 17:22:21Z kiall $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Cache_Core {

	protected static $instances = array();

	// Configuration
	protected $config;

	// Driver object
	protected $driver;

	/**
	 * Returns a singleton instance of Cache.
	 *
	 * @param   string  configuration
	 * @return  Cache_Core
	 */
	public static function & instance($config = FALSE)
	{
		if ( ! isset(Cache::$instances[$config]))
		{
			// Create a new instance
			Cache::$instances[$config] = new Cache($config);
		}

		return Cache::$instances[$config];
	}

	/**
	 * Loads the configured driver and validates it.
	 *
	 * @param   array|string  custom configuration or config group name
	 * @return  void
	 */
	public function __construct($config = FALSE)
	{
		if (is_string($config))
		{
			$name = $config;

			// Test the config group name
			if (($config = Kohana::config('cache.'.$config)) === NULL)
				throw new Kohana_Exception('cache.undefined_group', $name);
		}

		if (is_array($config))
		{
			// Append the default configuration options
			$config += Kohana::config('cache.default');
		}
		else
		{
			// Load the default group
			$config = Kohana::config('cache.default');
		}

		// Cache the config in the object
		$this->config = $config;

		// Set driver name
		$driver = 'Cache_'.ucfirst($this->config['driver']).'_Driver';

		// Load the driver
		if ( ! Kohana::auto_load($driver))
			throw new Kohana_Exception('core.driver_not_found', $this->config['driver'], get_class($this));

		// Initialize the driver
		$this->driver = new $driver($this->config['params']);

		// Validate the driver
		if ( ! ($this->driver instanceof Cache_Driver))
			throw new Kohana_Exception('core.driver_implements', $this->config['driver'], get_class($this), 'Cache_Driver');

		Kohana_Log::add('debug', 'Cache Library initialized');
	}

	/**
	 * Set cache items  
	 */
	public function set($key, $value = NULL, $tags = NULL, $lifetime = NULL)
	{
		if ($lifetime === NULL)
		{
			$lifetime = $this->config['lifetime'];
		}
		
		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		return $this->driver->set($key, $tags, $lifetime);
	}

	/**
	 * Get a cache items by key 
	 */
	public function get($keys)
	{
		$single = FALSE;

		if ( ! is_array($keys))
		{
			$keys = array($keys);
			$single = TRUE;
		}

		return $this->driver->get($keys, $single);
	}

	/**
	 * Get cache items by tags
	 */
	public function get_tag($tags)
	{
		if ( ! is_array($tags))
		{
			$tags = array($tags);
		}

		return $this->driver->get_tag($tags);
	}

	/**
	 * Delete cache item by key 
	 */
	public function delete($keys)
	{
		if ( ! is_array($keys))
		{
			$keys = array($keys);
		}

		return $this->driver->delete($keys);
	}

	/**
	 * Delete cache items by tag 
	 */
	public function delete_tag($tags)
	{
		if ( ! is_array($tags))
		{
			$tags = array($tags);
		}

		return $this->driver->delete_tag($tags);
	}

	/**
	 * Empty the cache
	 */
	public function delete_all()
	{
		return $this->driver->delete_all();
	}
} // End Cache Library