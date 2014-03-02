<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjToolkit
{
	public static function decrypt($str)
	{
		$txt = '';
		$arr = explode("%", $str);
		foreach ($arr as $val)
		{
			if (strlen($val) > 0)
			{
				$txt .= chr(hexdec($val));
			}
		}
		return $txt;
	}
	
	public static function download($data, $name, $mimetype='', $filesize=false)
	{
	    // File size not set?
	    if ($filesize == false || !is_numeric($filesize))
	    {
	        $filesize = strlen($data);
	    }
	
	    // Mimetype not set?
	    if (empty($mimetype))
	    {
	        $mimetype = 'application/octet-stream';
	        //$mimetype = 'application/force-download';
	    }
		
	    // Start sending headers
	    header("Pragma: public"); // required
	    header("Expires: 0"); // no cache
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header("Cache-Control: private",false); // required for certain browsers
	    header("Content-Transfer-Encoding: binary");
	    header("Content-Type: " . $mimetype);
	    header("Content-Length: " . $filesize);
	    header("Content-Disposition: attachment; filename=\"" . $name . "\";" );
	
		// download
		echo $data;
		//die();
	}
	
	public static function encodeEmail($str)
	{
		$output = "";
		for ($i = 0; $i < strlen($str); $i++)
		{
			$output .= '&#' . ord($str[$i]) . ';';
		}
		return $output;
	}
	
	public static function field($key)
    {
    	$fields = pjRegistry::getInstance()->get('fields');
    	return isset($fields[$key]) ? $fields[$key] : NULL;
    }
	
	public static function formatCurrencySign($price, $currency, $separator = "")
	{
		switch ($currency)
		{
			case 'USD':
				$format = "$" . $separator . $price;
				break;
			case 'GBP':
				$format = "&pound;" . $separator . $price;
				break;
			case 'EUR':
				$format = "&euro;" . $separator . $price;
				break;
			case 'JPY':
				$format = "&yen;" . $separator . $price;
				break;
			case 'AUD':
			case 'CAD':
			case 'NZD':
			case 'CHF':
			case 'HKD':
			case 'SGD':
			case 'SEK':
			case 'DKK':
			case 'PLN':
				$format = $price . $separator . $currency;
				break;
			case 'NOK':
			case 'HUF':
			case 'CZK':
			case 'ILS':
			case 'MXN':
				$format = $currency . $separator . $price;
				break;
			default:
				$format = $price . $separator . $currency;
				break;
		}
		return $format;
	}
	
	public static function formatDate($date, $inputFormat, $outputFormat = "Y-m-d")
	{
		if (empty($date))
		{
			return FALSE;
		}
		$limiters = array('.', '-', '/');
		foreach ($limiters as $limiter)
		{
			if (strpos($inputFormat, $limiter) !== false)
			{
				$_date = explode($limiter, $date);
				$_iFormat = explode($limiter, $inputFormat);
				$_iFormat = array_flip($_iFormat);
				break;
			}
		}
		if (!isset($_iFormat) || !isset($_date) || count($_date) !== 3)
		{
			return FALSE;
		}
		return date($outputFormat, mktime(0, 0, 0,
			$_date[isset($_iFormat['m']) ? $_iFormat['m'] : $_iFormat['n']],
			$_date[isset($_iFormat['d']) ? $_iFormat['d'] : $_iFormat['j']],
			$_date[$_iFormat['Y']]));
	}
	
	public static function formatSize($bytes)
	{
		$size = (int) $bytes / 1024;
		if ($size > 1023)
		{
			$size = round($size / 1024, 1) . " MB";
		} else {
			$size = ceil($size) . " KB";
		}
		return $size;
	}
	
	public static function formatTime($time, $inputFormat, $outputFormat = "H:i:s")
	{
		$limiters = array(':');
		foreach ($limiters as $limiter)
		{
			if (strpos($inputFormat, $limiter) !== false)
			{
				$_time = explode($limiter, $time);
				if (strpos($_time[1], " ") !== false)
				{
					list($_time[1], $_time[2]) = explode(" ", $_time[1]);
				}
				$_iFormat = explode($limiter, $inputFormat);
				if (strpos($_iFormat[1], " ") !== false)
				{
					list($_iFormat[1], $_iFormat[2]) = explode(" ", $_iFormat[1]);
				}
				$_iFormat = array_flip($_iFormat);
				break;
			}
		}
		
		$sec = 0;
		if (isset($_iFormat['a']))
		{
			if ($_time[$_iFormat['a']] == 'pm')
			{
				$sec = 60 * 60 * 12;
			}
		} elseif (isset($_iFormat['A'])) {
			if ($_time[$_iFormat['A']] == 'PM')
			{
				$sec = 60 * 60 * 12;
			}
		}

		return date($outputFormat, mktime(
			$_time[isset($_iFormat['G']) ? $_iFormat['G'] : (isset($_iFormat['g']) ? $_iFormat['g'] : (isset($_iFormat['H']) ? $_iFormat['H'] : $_iFormat['h']))],
			$_time[$_iFormat['i']],
			$sec,
			0, 0, 0
		));
	}
	
	public static function getDomain($url)
	{
		$host = @parse_url($url, PHP_URL_HOST);
		if ($host !== false && !empty($host))
		{
			# Check for IP address
			preg_match('/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/', $host, $match);
			if (isset($match[0]))
			{
				return $match[0];
			}
			# Check for domain
		    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $host, $regs))
		    {
		        return $regs['domain'];
		    }
		    # Check for localhost
		    if ($host == 'localhost')
		    {
		    	return $host;
		    }
		}
	    return null;
	}
	
	public static function getFileExtension($str)
    {
    	$arrSegments = explode('.', $str);
        $strExtension = $arrSegments[count($arrSegments) - 1];
        $strExtension = strtolower($strExtension);
        return $strExtension;
    }

	public static function getRandomPassword($n = 6, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890')
	{
		srand((double) microtime() * 1000000);
		$m = strlen($chars);
		$randPassword = "";
		while ($n--)
		{
			$randPassword .= substr($chars, rand() % $m, 1);
		}
		return $randPassword;
	}
	
	public static function html2rgb($color)
	{
		if ($color[0] == '#')
		{
			$color = substr($color, 1);
		}
		if (strlen($color) == 6)
		{
			list($red, $green, $blue) = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
		} elseif (strlen($color) == 3) {
			list($red, $green, $blue) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		} else {
			return false;
		}
		$red = hexdec($red);
		$green = hexdec($green);
		$blue = hexdec($blue);
		return array($red, $green, $blue);
	}
	
	public static function jqDateFormat($phpFormat)
	{
		$jQuery = array('d', 'dd', 'm', 'mm', 'yy');
		$php = array('j', 'd', 'n', 'm', 'Y');
		$limiters = array('.', '-', '/');
		foreach ($limiters as $limiter)
		{
			if (strpos($phpFormat, $limiter) !== false)
			{
				$_iFormat = explode($limiter, $phpFormat);
				return join($limiter, array(
					$jQuery[array_search($_iFormat[0], $php)],
					$jQuery[array_search($_iFormat[1], $php)],
					$jQuery[array_search($_iFormat[2], $php)]
				));
			}
		}
		return $phpFormat;
	}
	
	public static function jqTimeFormat($phpFormat)
	{
		$jQuery = array('HH', 'hh', 'H', 'h', 'mm', 'TT', 'tt');
		$php = array('H', 'h', 'G', 'g', 'i', 'A', 'a');
		$limiters = array(':');
		foreach ($limiters as $limiter)
		{
			if (strpos($phpFormat, $limiter) !== false)
			{
				$_iFormat = explode($limiter, $phpFormat);
				$_iFormat[2] = NULL;
				if (strpos($_iFormat[1], " ") !== false)
				{
					list($_iFormat[1], $_iFormat[2]) = explode(" ", $_iFormat[1]);
				}
				$result = join($limiter, array(
					$jQuery[array_search($_iFormat[0], $php)],
					$jQuery[array_search($_iFormat[1], $php)]
				));
				if (!is_null($_iFormat[2]))
				{
					$result .= " " . $jQuery[array_search($_iFormat[2], $php)];
				}
				return $result;
			}
		}
		return $phpFormat;
	}
	
	public static function jsDateFormat($phpFormat)
	{
		$js = array('d', 'dd', 'M', 'MM', 'yyyy');
		$php = array('j', 'd', 'n', 'm', 'Y');
		$limiters = array('.', '-', '/');
		foreach ($limiters as $limiter)
		{
			if (strpos($phpFormat, $limiter) !== false)
			{
				$_iFormat = explode($limiter, $phpFormat);
				return join($limiter, array(
					$js[array_search($_iFormat[0], $php)],
					$js[array_search($_iFormat[1], $php)],
					$js[array_search($_iFormat[2], $php)]
				));
			}
		}
		return $phpFormat;
	}
	
	public static function printNotice($title, $body, $convert = true, $close = true)
	{
		?>
		<div class="notice-box">
			<div class="notice-top"></div>
			<div class="notice-middle">
				<span class="notice-info">&nbsp;</span>
				<?php
				if (!empty($title))
				{
					printf('<span class="block bold">%s</span>', $convert ? htmlspecialchars(stripslashes($title)) : stripslashes($title));
				}
				if (!empty($body))
				{
					printf('<span class="block">%s</span>', $convert ? htmlspecialchars(stripslashes($body)) : stripslashes($body));
				}
				if ($close)
				{
					?><a href="#" class="notice-close"></a><?php
				}
				?>
			</div>
			<div class="notice-bottom"></div>
		</div>
		<?php
	}
	
	public static function readDir(&$data, $dir)
	{
		$stop = array('.', '..', '.buildpath', '.project', '.svn', 'Thumbs.db');
		if ($handle = opendir($dir))
		{
			$sep = $dir{strlen($dir)-1} != '/' ? '/' : NULL;
			while (false !== ($file = readdir($handle)))
			{
				if (in_array($file, $stop)) continue;
				if (!is_dir($dir . $sep . $file))
				{
					$data[] = $dir . $sep . $file;
				} else {
					pjToolkit::readDir($data, $dir . $sep . $file);
				}
			}
			closedir($handle);
		}
	}
	
	public static function redirect($url, $http_response_code = null, $exit = true)
	{
		if (strstr($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS'))
		{
			echo '<html><head><title></title><script type="text/javascript">window.location.href="'.$url.'";</script></head><body></body></html>';
		} else {
			$http_response_code = !is_null($http_response_code) && (int) $http_response_code > 0 ? $http_response_code : 303;
			header("Location: $url", true, $http_response_code);
		}
		if ($exit)
		{
	    	exit();
		}
	}
}
?>