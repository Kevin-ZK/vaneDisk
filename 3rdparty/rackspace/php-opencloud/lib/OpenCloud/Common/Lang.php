<?php
/**
 * PHP OpenCloud library.
 * 
 * @copyright 2014 Rackspace Hosting, Inc. See LICENSE for information.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 */

namespace OpenCloud\Common;

class Lang 
{
	
	public static function translate($word = null) 
	{
		return $word;
	}
	
	public static function noslash($str) 
	{
		while ($str && (substr($str, -1) == '/')) {
			$str = substr($str, 0, strlen($str) - 1);
		}
		return $str;
	}
	
}
