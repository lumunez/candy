<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once dirname(__FILE__) . '/pjUpload.component.php';
class pjImage extends pjUpload
{
	private $font;
	
	private $fontSize;
	
	private $image;
	
	private $imageType;
	
	private $fillColor = array(255, 255, 255);
		
	public function __construct()
	{
		if (!extension_loaded('gd') || !function_exists('gd_info'))
		{
			$this->error = "GD extension is not loaded";
			$this->errorCode = 200;
		}
	}
	
	public function crop($src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
	{
		$x = $this->getWidth();
		$y = $this->getHeight();

		if ($x < $y)
		{
			$tmp = $x;
			$x = $y;
			$y = $tmp;
		}
		$x = $x > $dst_w ? $dst_w : $x;
		$y = $y > $dst_h ? $dst_h : $y;

		$new_image = imagecreatetruecolor($dst_w, $dst_h);
		imagecopyresampled($new_image, $this->image, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
		
		imagefill($new_image, $x, $y, imagecolorallocate($new_image, $this->fillColor[0], $this->fillColor[1], $this->fillColor[2]));
		$this->image = $new_image;
		return $this;
	}
	
	public function getHeight()
	{
		return imagesy($this->image);
	}
	
	public function getImageSize()
    {
    	return getimagesize($this->file['tmp_name']);
    }
    
	public function getWidth()
	{
		return imagesx($this->image);
	}

	public function isConvertPossible()
	{
		$status = true;
		if (function_exists('memory_get_usage') && ini_get('memory_limit'))
		{
			$info = $this->getImageSize();
			$MB = 1024 * 1024;
			$K64 = 64 * 1024;
			$tweak_factor = 1.6;
			$channels = isset($info['channels']) ? $info['channels'] : 3; // 3 for RGB pictures and 4 for CMYK pictures
			$memory_needed = round(($info[0] * $info[1] * $info['bits'] * $channels / 8 + $K64) * $tweak_factor);
			$memory_needed = memory_get_usage() + $memory_needed;
			$memory_limit = ini_get('memory_limit');
			if ($memory_limit != '')
			{
				$memory_limit = substr($memory_limit, 0, -1) * $MB;
			}
			if ($memory_needed > $memory_limit)
			{
				//$memory_needed = round($memory_needed / 1024 / 1024, 2);
				$status = false;
			}
		}
		return compact('status', 'memory_needed', 'memory_limit');
	}
	
	public function loadImage($path=NULL)
	{
		if (!is_null($path))
		{
			$this->file = array(
				'tmp_name' => $path,
				'name' => basename($path)
			);
		}
		$info = $this->getImageSize();
		$this->imageType = $info[2];
		$file = $this->getFile('tmp_name');
		
		switch ($this->imageType)
		{
			case IMAGETYPE_JPEG:
				$this->image = @imagecreatefromjpeg($file);
				break;
			case IMAGETYPE_GIF:
				$this->image = @imagecreatefromgif($file);
				break;
			case IMAGETYPE_PNG:
				$this->image = @imagecreatefrompng($file);
				break;
		}
		return $this;
	}
	
	public function resize($width, $height)
	{
		$new_image = imagecreatetruecolor($width, $height);
		if ($this->imageType == IMAGETYPE_PNG)
		{
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
		}
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
		
		return $this;
	}
	
	public function resizeSmart($width, $height)
	{
		$h = $this->getHeight();
		$w = $this->getWidth();
		
		if ($w == $h)
		{
			if ($width > $height)
			{
				$this->resizeToWidth($width > $w ? $w : $width);
			} else {
				$this->resizeToHeight($height > $h ? $h : $height);
			}
			$this->crop(0, 0, $width, $height, $width, $height);
			return $this;
		}
		
		if ($w < $h)
		{
			# Uploaded (or given one) image is vertical [300 x 400]
			if ($height > $h)
			{
				# Needed image is bigger than uploaded (or given one) [400 x 600], [800 x 600]
				if ($width < $height)
				{
					# We want to get vertical image [400 x 600]
					$this->crop(0, 0, $width, $height, $width, $height);
				} else {
					# We want to get horizontal image [800 x 600]
					$this->crop(0, 0, $width, $height, $width, $height);
				}
				return $this;
			} else {
				# Needed image is smaller than uploaded (or given one) [90 x 68], [75 x 110]
				if ($width < $height)
				{
					# We want to get vertical image [75 x 110]
					$index = $h / $height;
				} else {
					# We want to get horizontal image [90 x 68]
					$index = $w / $width;
				}
				$this->crop(0, 0, ceil($width * $index), ceil($height * $index), ceil($width * $index), ceil($height * $index));
				$this->resize($width, $height);
				return $this;
			}
		} else {
			# Uploaded (or given one) image is horizontal [600 x 400]
			if ($width > $w)
			{
				# Needed image is bigger than uploaded (or given one) [768 x 1024], [800 x 600]
				if ($width < $height)
				{
					# We want to get vertical image [768 x 1024]
					$this->crop(0, 0, $width, $height, $width, $height);
				} else {
					# We want to get horizontal image [800 x 600]
					$this->crop(0, 0, $width, $height, $width, $height);
				}
				return $this;
			} else {
				# Needed image is smaller than uploaded (or given one) [90 x 68], [75 x 110]
				if ($width == $height)
				{
					$this->resizeToWidth($width > $w ? $w : $width);
					$this->crop(0, 0, $width, $height, $width, $height);
					return $this;
				}
				if ($width < $height)
				{
					# We want to get vertical image [75 x 110]
					$index = $w / $width;
				} else {
					# We want to get horizontal image [90 x 68]
					$index = $h / $height;
				}
				$this->crop(0, 0, ceil($width * $index), ceil($height * $index), ceil($width * $index), ceil($height * $index));
				$this->resize($width, $height);
				return $this;
			}
		}
		
		return $this;
	}

	public function resizeToHeight($height)
	{
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width, $height);
		return $this;
	}

	public function resizeToWidth($width)
	{
		$ratio = $width / $this->getWidth();
		$height = $this->getHeight() * $ratio;
		$this->resize($width, $height);
		return $this;
	}
	
	public function rotate($degrees=-90)
	{
		$this->image = imagerotate($this->image, $degrees, 0);
		return $this;
	}

	public function saveImage($dst, $image_type=IMAGETYPE_JPEG, $compression=100, $permissions=null)
	{
		switch ($image_type)
		{
			case IMAGETYPE_JPEG:
				imagejpeg($this->image, $dst, $compression);
				break;
			case IMAGETYPE_GIF:
				imagegif($this->image, $dst);
				break;
			case IMAGETYPE_PNG:
				imagepng($this->image, $dst);
				break;
		}
		if ($permissions != null)
		{
			chmod($dst, $permissions);
		}
		return $this;
	}
	
	public function setWatermark($text, $position)
	{
		$color = imagecolorallocate($this->image, 255, 255, 255);
		// Simple text
		//$font = imageloadfont($this->font);
		//imagestring($this->image, $font, 0, $this->getHeight() / 2, $text, $color);
		//imagestring($this->image, 5, 0, $this->getHeight() / 2, $text, $color);
		
		$tb = imagettfbbox($this->fontSize, 0, $this->font, $text);
		
		switch ($position)
		{
			case 'tl':
				$x = $tb[0];
				$y = $this->fontSize;
				break;
			case 'tr':
				$x = floor($this->getWidth() - $tb[2]);
				$y = $this->fontSize;
				break;
			case 'tc':
				$x = ceil(($this->getWidth() - $tb[2]) / 2);
				$y = $this->fontSize;
				break;
			case 'bl':
				$x = $tb[0];
				$y = floor($this->getHeight() - $this->fontSize);
				break;
			case 'br':
				$x = floor($this->getWidth() - $tb[2]);
				$y = floor($this->getHeight() - $this->fontSize);
				break;
			case 'bc':
				$x = ceil(($this->getWidth() - $tb[2]) / 2);
				$y = floor($this->getHeight() - $this->fontSize);
				break;
			case 'cl':
				$x = $tb[0];
				$y = ceil($this->getHeight() / 2);
				break;
			case 'cr':
				$x = floor($this->getWidth() - $tb[2]);
				$y = ceil($this->getHeight() / 2);
				break;
			case 'cc':
			default:
				$x = ceil(($this->getWidth() - $tb[2]) / 2);
				$y = ceil($this->getHeight() / 2);
				break;
		}
		imagettftext($this->image, $this->fontSize, 0, $x, $y, $color, $this->font, $text);
		return $this;
	}

	public function setFont($path)
	{
		$this->font = $path;
		return $this;
	}
	
	public function setFontSize($size)
	{
		$this->fontSize = $size;
		return $this;
	}
	
	public function setFillColor($color)
	{
		if (is_array($color) && count($color) === 3)
		{
			$this->fillColor = $color;
		}
		return $this;
	}
}
?>