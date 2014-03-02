<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once ROOT_PATH . 'core/framework/components/pjToolkit.component.php';
class pjUtil extends pjToolkit
{
	public static function concat($glue = '', $array = array())
	{
		$pieces = array();
		
		foreach ($array as $key => $val)
		{
			if (!empty($val))
			{
				$pieces[] = $val;
			}
		}
		
		return implode(', ', $pieces);
	}
	
	public static function showFloor($opt, $sqm, $lang=array())
	{
		switch (strtolower($opt))
		{
			case 'sq.f.':
				//$sqf = $sqm  * 10.7639104;
				$sqf = $sqm;
				return sprintf("%s %s", $sqf, $lang['sq.f.']);
				break;
			case 'sq.m.':
			default:
				return sprintf("%s %s", $sqm, $lang['sq.m.']);
				break;
		}
	}
	
	static public function uuid()
	{
		return chr(rand(65,90)) . chr(rand(65,90)) . time();
	}
	
	static public function convertLinks($text)
	{
		$text = preg_replace('/(((f|ht){1}tps?:\/\/)[-a-zA-Z0-9@:;%_\+.~#?&\/\/=]+)/', '<a href="\\1" target="_blank">\\1</a>', $text);
		$text = preg_replace('/([[:space:]()[{}])?(www.[-a-zA-Z0-9@:;%_\+.~#?&\/\/=]+)/', '\\1<a href="http://\\2" target="_blank">\\2</a>', $text);
		$text = preg_replace('/(([0-9a-zA-Z\.\-\_]+)@([0-9a-zA-Z\.\-\_]+)\.([0-9a-zA-Z\.\-\_]+))/', '<a href="mailto:$1">$1</a>', $text);
		return $text;
	}
}

function __($key, $return=false)
{
	$text = pjUtil::field($key);
	if ($return)
	{
		return $text;
	}
	echo $text;
}

function __autoload($className)
{
	$paths = array(
		PJ_FRAMEWORK_PATH . $className . '.class.php',
		PJ_CONTROLLERS_PATH . $className . '.controller.php',
		PJ_MODELS_PATH . str_replace('Model', '', $className) . '.model.php',
		PJ_COMPONENTS_PATH. $className . '.component.php',
		PJ_FRAMEWORK_PATH . 'components/'. $className . '.component.php'
	);

	foreach ($paths as $filename)
	{
		if (is_file($filename))
		{
			require $filename;
			return;
		}
	}
}
?>