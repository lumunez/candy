<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjHttp
{
/**
 *
 * Connect timeout default
 * @var integer
 * @access private
 */
	private $connectTimeout = 30;
/**
 *
 * Hold POST data
 * @var array|string
 * @access private
 */
	private $data = NULL;
/**
 * Hold error code/text
 *
 * @var array
 * @access private
 */
	private $error = array();
/**
 *
 * Hold request headers
 * @var array
 * @access private
 */
	private $headers = array();
/**
 *
 * API base URL
 * @var string
 * @access private
 */
	private $host = "http://127.0.0.1:3000/api/v1";
/**
 *
 * Last HTTP status code
 * @var string
 * @access private
 */
	private $httpCode;
/**
 *
 * Last HTTP headers
 * @var string
 * @access private
 */
	private $httpInfo;
/**
 * Requesting method
 *
 * @var string
 * @access private
 */
	private $method = 'GET';
/**
 *
 * Password for basic authentification
 * @var string
 * @access private
 */
	private $password;
/**
 *
 * Hold response
 * @var mixed
 * @access private
 */
	private $response = NULL;
/**
 *
 * Hold response headers
 * @var array
 * @access private
 */
	private $responseHeaders = array();
/**
 *
 * Verify SSL Cert?
 * @var boolean
 * @access private
 */
	private $sslVerifyPeer = FALSE;
/**
 *
 * Timeout default
 * @var integer
 * @access private
 */
	private $timeout = 30;
/**
 *
 * Last API call
 * @var string
 * @access private
 */
	private $url;
/**
 *
 * Username for basic authentification
 * @var string
 * @access private
 */
	private $username;
/**
 *
 * PHPJabbers PHP Library
 * @var string
 * @access private
 */
	private $userAgent = "StivaSoft PHP Library";
  
	public function getError()
	{
		return $this->error;
	}
/**
 *
 * Get the header info to store.
 * @param mixed $ch
 * @param string $header
 * @return integer
 */
	public function pjActionGetHeader($ch, $header)
	{
		$i = strpos($header, ':');
		if (!empty($i))
		{
			$key = strtolower(substr($header, 0, $i));
			$value = trim(substr($header, $i + 2));
			$this->responseHeaders[$key] = $value;
		}
		return strlen($header);
	}
  
	public function curlRequest($url)
	{
		$this->httpInfo = array();
		$ch = curl_init();

	    curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerifyPeer);
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'pjActionGetHeader'));
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		if (!empty($this->username) && !empty($this->password))
		{
			curl_setopt($ch, CURLOPT_USERPWD, sprintf("%s:%s", $this->username, $this->password));
		}

		$post_fields = $this->getData();
		
		switch ($this->getMethod())
		{
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, TRUE);
				if (!empty($post_fields))
				{
					if (is_array($post_fields))
					{
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data"));
					}
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
				}
				break;
			case 'DELETE':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($post_fields))
				{
					$url = "{$url}?{$post_fields}";
				}
				break;
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		$this->response = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->httpInfo = array_merge($this->httpInfo, curl_getinfo($ch));
		$this->url = $url;
		curl_close($ch);
		return $this;
	}
	
	public function socketRequest($url)
	{
		$parts = parse_url($url);
		$port = $parts['scheme'] == 'https' ? 443 : 80;
		$fp = @fsockopen($parts['host'], $port, $errno, $errstr, $this->connectTimeout);
		if (!$fp)
		{
		    $this->error = array('text' => $errstr, 'code' => $errno);
		} else {
			$data = NULL;
			switch ($this->getMethod())
			{
				case 'GET':
					$out = "GET ".$parts['path'].(isset($parts['query']) ? "?".$parts['query'] : NULL)." HTTP/1.1\r\n";
					break;
				case 'POST';
					$out = "POST ".$parts['path'].(isset($parts['query']) ? "?".$parts['query'] : NULL)." HTTP/1.1\r\n";
					
					$data = $this->getData();
					$this->addHeader("Content-Type: application/x-www-form-urlencoded");
					$this->addHeader("Content-Length: " . strlen($data));
					break;
			}
			$out .= "Host: ".$parts['host']."\r\n";
			if (!empty($this->username) && !empty($this->password))
			{
				$this->addHeader("Authorization: Basic " . base64_encode($this->username .":". $this->password));
			}
			foreach ($this->getHeaders() as $header)
			{
				$out .= $header."\r\n";
			}
		    $out .= "Connection: Close\r\n\r\n";

		    fwrite($fp, $out);
		    if (!is_null($data))
		    {
		    	fwrite($fp, $data);
		    }
		    $response = '';
        	$header = "not yet";
		    while (!feof($fp))
		    {
				$line = fgets($fp, 128);
				$this->pjActionGetHeader(NULL, $line);
				
				if ($line == "\r\n" && $header == "not yet")
				{
					$header = "passed";
				}
				if ($header == "passed")
				{
					//$response .= preg_replace('/\n|\r\n/', '', $line);
					$response .= $line;
				}
		    }
		    fclose($fp);
		    $this->response = $response;
		}
		$this->url = $url;
		return $this;
	}
	
	public function fileRequest($url)
	{
		$response = @file_get_contents($url);
		if (!$response)
		{
			$this->error = array('code' => 100, 'text' => 'An error occurs');
			return $this;
		}
		$this->response = $response;
		$this->url = $url;
		return $this;
	}
	
	public function streamRequest($url)
	{
		$handle = @fopen($url, 'r');
		if (!$handle)
		{
			$this->error = array('code' => 100, 'text' => 'An error occurs');
			return $this;
		}
		$this->response = stream_get_contents($handle);
		$this->url = $url;
		fclose($handle);
		return $this;
	}
	
	public function request($url)
	{
		if (function_exists('curl_init'))
		{
			$this->curlRequest($url);
		} elseif (function_exists('file_get_contents')) {
			$this->fileRequest($url);
		} elseif (function_exists('fsockopen')) {
			$this->socketRequest($url);
		} elseif (function_exists('stream_get_contents')) {
			$this->streamRequest($url);
		}
		return $this;
	}
	
	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}
	
	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}
	
	public function setHost($host)
	{
		$this->host = $host;
		return $this;
	}

	public function getData()
	{
		return $this->data;
	}
	
	public function getHost()
	{
		return $this->host;
	}
    	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function getResponse()
	{
		return $this->response;
	}
	
	public function getResponseHeaders()
	{
		return $this->responseHeaders;
	}
	
	public function setMethod($method)
	{
		$this->method = strtoupper($method);
		return $this;
	}
	
	public function getHeaders()
	{
		return $this->headers;
	}
	
	public function setData($data)
	{
		if (is_array($data))
		{
			$data = http_build_query($data);
		}
		$this->data = $data;
		return $this;
	}
	
	public function setHeaders($headers)
	{
		$this->headers = array();

		foreach ($this->flattenHeaders($headers) as $header)
		{
			$this->addHeader($header);
		}
		
		return $this;
    }

    public function addHeader($header)
	{
		if (0 === stripos(substr($header, -8), 'HTTP/1.') && 3 == count($parts = explode(' ', $header)))
		{
			list($method, $resource, $protocolVersion) = $parts;

			$this->setMethod($method);
			//$this->setResource($resource);
			//$this->setProtocolVersion((float) substr($protocolVersion, 5));
		} else {
			$this->headers[] = $header;
		}
		return $this;
    }
    
	protected function flattenHeaders(array $headers)
    {
        $flattened = array();
        foreach ($headers as $key => $header)
        {
			if (is_int($key))
			{
				$flattened[] = $header;
			} else {
				$flattened[] = $key.': '.$header;
			}
		}

		return $flattened;
    }
}
?>