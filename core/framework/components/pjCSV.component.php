<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCSV
{
	private $delimiter = ",";
	
	private $eol = "\n";
	
	private $enclose = '"';
	
	private $escape = '"';
	
	private $header = false;
	
	private $data = NULL;
	
	private $name = NULL;
	
	private $fields = array();
	
	public function __construct()
	{
		$this->name = time() . ".csv";
	}
	
	public function download()
	{
		pjToolkit::download($this->data, $this->name, 'text/csv');
	}
	
	public function process($data=array())
	{
		$i = 0;
		$keys = $rows = array();
		foreach ($data as $item)
		{
			if ($i === 0)
			{
				$keys = array_keys($item);
			}
			$cells = array();
			foreach ($item as $value)
			{
				$cells[] = $this->enclose . preg_replace('/'.$this->enclose.'/', $this->escape . $this->enclose, $value) . $this->enclose;
			}
			$rows[] = join($this->delimiter, $cells);
			$i++;
		}
		if ($this->header)
		{
			array_unshift($rows, join($this->delimiter, $keys));
		}
		$this->data = join($this->eol, $rows);
		
		return $this;
	}
	
	public function write()
	{
		file_put_contents($this->name, $this->data);
		return $this;
	}
	
	public function load($file)
	{
		$pjUpload = new pjUpload();
		$pjUpload->setAllowedExt(array('csv'));

		$data = array();
		if ($pjUpload->load($file))
		{
			$filename = $pjUpload->getFile('tmp_name');
			$i = 1;
			if (($handle = fopen($filename, "r")) !== FALSE)
			{
				while (($values = fgetcsv($handle, 1000, ",")) !== FALSE)
				{
					if ($i == 1)
					{
						$keys = $values;
					} else {
						$data[] = array_combine($keys, $values);
					}
					$i++;
				}
				fclose($handle);
				$this->data = $data;
				return true;
			}
		}
		return false;
	}
	
	public function import($modelName)
	{
		if (is_array($this->data) && count($this->data) > 0)
		{
			$modelName .= 'Model';
			$model = new $modelName;
			if (is_object($model))
			{
				$model->begin();
				foreach ($this->data as $data)
				{
					if (count($this->fields) > 0)
					{
						foreach ($data as $k => $v)
						{
							if (!array_key_exists($k, $this->fields))
							{
								unset($data[$k]);
							}
						}
					}
					$model->reset()->setAttributes($data)->insert();
				}
				$model->commit();
			}
		}
		
		return $this;
	}
	
	public function setDelimiter($value)
	{
		$this->delimiter = $value;
		return $this;
	}
	
	public function setEol($value)
	{
		$this->eol = $value;
		return $this;
	}
	
	public function setEnclose($value)
	{
		$this->enclose = $value;
		return $this;
	}
	
	public function setEscape($value)
	{
		$this->escape = $value;
		return $this;
	}
	
	public function setHeader($value)
	{
		$this->header = (bool) $value;
		return $this;
	}
	
	public function setName($value)
	{
		$this->name = $value;
		return $this;
	}

	public function setFields($value)
	{
		if (is_array($value))
		{
			$this->fields = $value;
		}
		return $this;
	}
}
?>