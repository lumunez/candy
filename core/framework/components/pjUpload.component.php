<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjUpload
{
	protected $file;
	
	protected $error;
	
	protected $errorCode;
	
	private $allowedTypes = array('*'); # image/gif, image/png, image/jpeg, image/jpg, image/pjpeg
	
	private $allowedExt = array('*'); # pdf, doc, png, txt, gif

	public function __construct()
	{
		
	}

	public function load($file)
	{
		$this->error = NULL;
		$this->errorCode = NULL;

		if (is_array($file) && array_key_exists('tmp_name', $file) && !empty($file['tmp_name']) &&
			is_uploaded_file($file['tmp_name']) && $file['error'] == UPLOAD_ERR_OK)
		{
			$this->file = $file;
			
			$ext = $this->getExtension();
			if (in_array($ext, $this->allowedExt) || in_array('*', $this->allowedExt))
			{
				//ok
			} else {
				$this->error = "File extension not supported. Supported file formats: " . join(", ", $this->allowedExt);
				$this->errorCode = "101";
			}
			if ((array_key_exists('type', $file) && in_array($file['type'], $this->allowedTypes)) || in_array('*', $this->allowedTypes))
			{
				//ok
			} else {
				$this->error = "Mime type not supported. Supported mime types: " . join(", ", $this->allowedTypes);
				$this->errorCode = "102";
			}
		} else {
			switch ($file['error'])
			{
				case UPLOAD_ERR_INI_SIZE:
					$this->error = "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
					$this->errorCode = "121";
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$this->error = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
					$this->errorCode = "122";
					break;
				case UPLOAD_ERR_PARTIAL:
					$this->error = "The uploaded file was only partially uploaded.";
					$this->errorCode = "123";
					break;
				case UPLOAD_ERR_NO_FILE:
					$this->error = "No file was uploaded.";
					$this->errorCode = "124";
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$this->error = "Missing a temporary folder.";
					$this->errorCode = "126";
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$this->error = "Failed to write file to disk.";
					$this->errorCode = "127";
					break;
				case UPLOAD_ERR_EXTENSION:
					$this->error = "A PHP extension stopped the file upload.";
					$this->errorCode = "128";
					break;
				default:
					$this->error = "The file is empty or wasn't uploaded via HTTP POST";
					$this->errorCode = "100";
			}
		}
		
		if (empty($this->error))
		{
			return true;
		}
		return false;
	}
	
	public function save($destination)
	{
		if (!move_uploaded_file($this->file['tmp_name'], $destination))
		{
			$this->error = $this->file['name'] . " is not a valid upload file or cannot be moved for some reason.";
			return false;
		}
		return true;
	}
	
	public function getError()
	{
		return $this->error;
	}
	
	public function getErrorCode()
	{
		return $this->errorCode;
	}
	
	public function getExtension()
    {
    	$arr = explode('.', $this->file['name']);
        $ext = strtolower($arr[count($arr) - 1]);
        return $ext;
    }
    
    public function getSize()
    {
    	return filesize($this->file['tmp_name']);
    }
    
    public function getFile($key)
    {
    	return $this->file[$key];
    }

	public function setAllowedTypes($value)
	{
		if (is_array($value))
		{
			$this->allowedTypes = $value;
		}
		return $this;
	}
	
	public function setAllowedExt($value)
	{
		if (is_array($value))
		{
			$this->allowedExt = $value;
		}
		return $this;
	}
}
?>