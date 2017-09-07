<?php
/**
 * Created by PhpStorm.
 * User: Tomas
 * Date: 3. 12. 2014
 * Time: 0:43
 */

namespace Lenny\Utils;


use Nette\Database\Table\ActiveRow;
use Nette\DI\Container;
use Nette\Object;
use Nette\Utils\DateTime;

class Utils extends Object {
	public static function parseResourceFiles($collection)
	{
		$files = array();
		foreach($collection as $file)
		{
			$files[] = WWW_DIR . '/' . ltrim($file, '/');
		}

		return $files;
	}

	/**
	 * @param $date
	 * @return bool
	 */
	public static function validateDate($date)
	{
		if($date instanceof DateTime) return true;

		$d = \DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') == $date;
	}
} 