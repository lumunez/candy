<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCaptcha
{
	private $font = null;
	
	private $fontSize = 12;
	
	private $height = 35;
	
	private $image = null;
	
	private $length = null;
	
	private $sessionVariable = null;
	
	private $width = 79;

	public function __construct($fontPath, $sessionVariable, $length = 4)
	{
		$this->font = $fontPath;
		$this->sessionVariable = $sessionVariable;
		$this->length = intval($length);
	}
	
	public function init($renew=null)
	{
    	if (!is_null($renew))
    	{
    		$_SESSION[$this->sessionVariable] = NULL;
    	}

		if (empty($_SESSION[$this->sessionVariable]))
		{
			$str = "";
			$length = 0;
			for ($i = 0; $i < $this->length; $i++)
			{
				//this numbers refer to numbers of the ascii table (small-caps)
				// 97 - 122 (small-caps)
				// 65 - 90 (all-caps)
				// 48 - 57 (digits 0-9)
				$str .= chr(rand(65, 90));
			}
			$_SESSION[$this->sessionVariable] = $str;
			$rand_code = $_SESSION[$this->sessionVariable];
		} else {
			$rand_code = $_SESSION[$this->sessionVariable];
		}

		if (!is_null($this->image))
		{
			$image = imagecreatefrompng($this->image);
		} else {
			$image = imagecreatetruecolor($this->width, $this->height);
			
			$backgr_col = imagecolorallocate($image, 204, 204, 204);
			$border_col = imagecolorallocate($image, 153, 153, 153);
			
			imagefilledrectangle($image, 0, 0, $this->width, $this->height, $backgr_col);
			imagerectangle($image, 0, 0, $this->width - 1, $this->height - 1, $border_col);
		}
		
		$text_col = imagecolorallocate($image, 68, 68, 68);

		$angle = rand(-10, 10);
		$box = imagettfbbox($this->fontSize, $angle, $this->font, $rand_code);
		$x = (int)($this->width - $box[4]) / 2;
		$y = (int)($this->height - $box[5]) / 2;
		imagettftext($image, $this->fontSize, $angle, $x, $y, $text_col, $this->font, $rand_code);
		
		header("Content-type: image/png");
		imagepng($image);
		imagedestroy ($image);
	}
	
	public function setFont($fontPath)
	{
		$this->font = $fontPath;
		return $this;
	}
	
	public function setLength($length)
	{
		if ((int) $length > 0)
		{
			$this->length = intval($length);
		}
		return $this;
	}
	
	public function setSessionVariable($sessionVariable)
	{
		$this->sessionVariable = $sessionVariable;
		return $this;
	}
	
	public function setHeight($height)
	{
		if ((int) $height > 0)
		{
			$this->height = intval($height);
		}
		return $this;
	}
	
	public function setWidth($width)
	{
		if ((int) $width > 0)
		{
			$this->width = intval($width);
		}
		return $this;
	}

	public function setFontSize($fontSize)
	{
		if ((int) $fontSize > 0)
		{
			$this->fontSize = intval($fontSize);
		}
		return $this;
	}

	public function setImage($image)
	{
		$this->image = $image;
		return $this;
	}
}
?>