<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Array helper class.
 *
 * $Id$
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class arr_Core {

	/**
	 * Return a callback array from a string, eg: limit[10,20] would become
	 * array('limit', array('10', '20'))
	 *
	 * @param   string  callback string
	 * @return  array
	 */
	public static function callback_string($str)
	{
		// command[param,param]
		if (preg_match('/([^\[]*+)\[(.+)\]/', (string) $str, $match))
		{
			// command
			$command = $match[1];

			// param,param
			$params = preg_split('/(?<!\\\\),/', $match[2]);
			$params = str_replace('\,', ',', $params);
		}
		else
		{
			// command
			$command = $str;

			// No params
			$params = NULL;
		}

		return array($command, $params);
	}

	/**
	 * Rotates a 2D array clockwise.
	 * Example, turns a 2x3 array into a 3x2 array.
	 *
	 * @param   array    array to rotate
	 * @param   boolean  keep the keys in the final rotated array. the sub arrays of the source array need to have the same key values.
	 *                   if your subkeys might not match, you need to pass FALSE here!
	 * @return  array
	 */
	public static function rotate($source_array, $keep_keys = TRUE)
	{
		$new_array = array();
		foreach ($source_array as $key => $value)
		{
			$value = ($keep_keys === TRUE) ? $value : array_values($value);
			foreach ($value as $k => $v)
			{
				$new_array[$k][$key] = $v;
			}
		}

		return $new_array;
	}

	/**
	 * Removes a key from an array and returns the value.
	 *
	 * @param   string  key to return
	 * @param   array   array to work on
	 * @return  mixed   value of the requested array key
	 */
	public static function remove($key, & $array)
	{
		if ( ! array_key_exists($key, $array))
			return NULL;

		$val = $array[$key];
		unset($array[$key]);

		return $val;
	}


	/**
	 * Extract one or more keys from an array. Each key given after the first
	 * argument (the array) will be extracted. Keys that do not exist in the
	 * search array will be NULL in the extracted data.
	 *
	 * @param   array   array to search
	 * @param   string  key name
	 * @return  array
	 */
	public static function extract(array $search, $keys)
	{
		// Get the keys, removing the $search array
		$keys = array_slice(func_get_args(), 1);

		$found = array();
		foreach ($keys as $key)
		{
			if (isset($search[$key]))
			{
				$found[$key] = $search[$key];
			}
			else
			{
				$found[$key] = NULL;
			}
		}

		return $found;
	}

	/**
	 * Because PHP does not have this function.
	 *
	 * @param   array   array to unshift
	 * @param   string  key to unshift
	 * @param   mixed   value to unshift
	 * @return  array
	 */
	public static function unshift_assoc( array & $array, $key, $val)
	{
		$array = array_reverse($array, TRUE);
		$array[$key] = $val;
		$array = array_reverse($array, TRUE);

		return $array;
	}

	/**
	 * Because PHP does not have this function, and array_walk_recursive creates
	 * references in arrays and is not truly recursive.
	 *
	 * @param   mixed  callback to apply to each member of the array
	 * @param   array  array to map to
	 * @return  array
	 */
	public static function map_recursive($callback, array $array)
	{
		foreach ($array as $key => $val)
		{
			// Map the callback to the key
			$array[$key] = is_array($val) ? arr::map_recursive($callback, $val) : call_user_func($callback, $val);
		}

		return $array;
	}

	/**
	 * @param mixed $needle     the value to search for
	 * @param array $haystack   an array of values to search in
	 * @param boolean $sort     sort the array now
	 * @return integer|FALSE    the index of the match or FALSE when not found
	 */
	public static function binary_search($needle, $haystack, $sort = FALSE)
	{
		if ($sort)
		{
			sort($haystack);
		}

		$high = count($haystack) - 1;
		$low = 0;

		while ($low <= $high)
		{
			$mid = ($low + $high) >> 1;

			if ($haystack[$mid] < $needle)
			{
				$low = $mid + 1;
			}
			elseif ($haystack[$mid] > $needle)
			{
				$high = $mid - 1;
			}
			else
			{
				return $mid;
			}
		}

		return FALSE;
	}


	/**
	 * Emulates array_merge_recursive, but appends numeric keys and replaces
	 * associative keys, instead of appending all keys.
	 *
	 * @param   array  any number of arrays
	 * @return  array
	 */
	public static function merge()
	{
		$total = func_num_args();

		$result = array();
		for ($i = 0; $i < $total; $i++)
		{
			foreach (func_get_arg($i) as $key => $val)
			{
				if (isset($result[$key]))
				{
					if (is_array($val))
					{
						// Arrays are merged recursively
						$result[$key] = arr::merge($result[$key], $val);
					}
					elseif (is_int($key))
					{
						// Indexed arrays are appended
						array_push($result, $val);
					}
					else
					{
						// Associative arrays are replaced
						$result[$key] = $val;
					}
				}
				else
				{
					// New values are added
					$result[$key] = $val;
				}
			}
		}

		return $result;
	}

	/**
	 * Overwrites an array with values from input array(s).
	 * Non-existing keys will not be appended!
	 *
	 * @param   array   key array
	 * @param   array   input array(s) that will overwrite key array values
	 * @return  array
	 */
	public static function overwrite($array1, $array2)
	{
		foreach (array_intersect_key($array2, $array1) as $key => $value)
		{
			$array1[$key] = $value;
		}

		if (func_num_args() > 2)
		{
			foreach (array_slice(func_get_args(), 2) as $array2)
			{
				foreach (array_intersect_key($array2, $array1) as $key => $value)
				{
					$array1[$key] = $value;
				}
			}
		}

		return $array1;
	}

	/**
	 * Fill an array with a range of numbers.
	 *
	 * @param   integer  stepping
	 * @param   integer  ending number
	 * @return  array
	 */
	public static function range($step = 10, $max = 100)
	{
		if ($step < 1)
			return array();

		$array = array();
		for ($i = $step; $i <= $max; $i += $step)
		{
			$array[$i] = $i;
		}

		return $array;
	}

	/**
	 * Recursively convert an array to an object.
	 *
	 * @param   array   array to convert
	 * @return  object
	 */
	public static function to_object(array $array, $class = 'stdClass')
	{
		$object = new $class;

		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				// Convert the array to an object
				$value = arr::to_object($value, $class);
			}

			// Add the value to the object
			$object->{$key} = $value;
		}

		return $object;
	}

} // End arr
