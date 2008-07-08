<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Eaccelerator-based Cache driver.
 *
 * $Id$
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Cache_Eaccelerator_Driver implements Cache_Driver {

	public function __construct()
	{
		if ( ! extension_loaded('eaccelerator'))
			throw new Kohana_Exception('cache.extension_not_loaded', 'eaccelerator');
	}

	public function get($id)
	{
		return (($return = eaccelerator_get($id)) === NULL) ? FALSE : $return;
	}

	public function find($tag)
	{
		return FALSE;
	}

	public function set($id, $data, $tags, $lifetime)
	{
		count($tags) and Log::add('error', 'tags are unsupported by the eAccelerator driver');

		return eaccelerator_put($id, $data, $lifetime);
	}

	public function delete($id, $tag = FALSE)
	{
		if ($id === TRUE)
			return eaccelerator_clean();

		if ($tag == FALSE)
			return eaccelerator_rm($id);

		return TRUE;
	}

	public function delete_expired()
	{
		eaccelerator_gc();
	}

} // End Cache eAccelerator Driver