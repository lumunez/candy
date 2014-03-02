<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once dirname(__FILE__) . '/pjInstallerAppController.controller.php';
class pjInstaller extends pjInstallerAppController
{
	public $defaultInstaller = 'Installer';
	
	public $defaultErrors = 'Errors';
	
	public function beforeFilter()
	{
		$this->appendJs('jquery-1.8.3.min.js', $this->getConst('PLUGIN_LIBS_PATH'));
		$this->appendCss('admin.css');
		$this->appendCss('install.css', $this->getConst('PLUGIN_CSS_PATH'));
		$this->appendCss('pj-button.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		$this->appendCss('pj-form.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
	}

	private static function pjActionImportSQL($file, $prefix, $scriptPrefix=NULL)
	{
		ob_start();
		readfile($file);
		$string = ob_get_contents();
		ob_end_clean();
		if ($string !== false)
		{
			$string = preg_replace(
				array(
					'/INSERT\s+INTO\s+`/',
					'/DROP\s+TABLE\s+`/',
					'/DROP\s+TABLE\s+IF\s+EXISTS\s+`/',
					'/CREATE\s+TABLE\s+`/',
					'/CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+`/'
				),
				array(
					'INSERT INTO `'.$prefix.$scriptPrefix,
					'DROP TABLE `'.$prefix.$scriptPrefix,
					'DROP TABLE IF EXISTS `'.$prefix.$scriptPrefix,
					'CREATE TABLE `'.$prefix.$scriptPrefix,
					'CREATE TABLE IF NOT EXISTS `'.$prefix.$scriptPrefix
				),
				$string);
			
			$arr = preg_split('/;(\s+)?\n/', $string);
			foreach ($arr as $v)
			{
				$v = trim($v);
				if (!empty($v))
				{
					if (!mysql_query($v))
					{
						return mysql_error();
					}
				}
			}
			return true;
		}
		return false;
	}

	private static function pjActionGetPaths()
	{
		$absolutepath = str_replace("\\", "/", dirname(realpath(basename(getenv("SCRIPT_NAME")))));
		$localpath = str_replace("\\", "/", dirname(getenv("SCRIPT_NAME")));
		
		$localpath = str_replace("\\", "/", $localpath);
		$localpath = preg_replace('/^\//', '', $localpath, 1) . '/';
		$localpath = !in_array($localpath, array('/', '\\')) ? $localpath : NULL;

		return array(
			'install_folder' => '/' . $localpath,
			'install_path' => $absolutepath . '/',
			'install_url' => 'http://' . $_SERVER['SERVER_NAME'] . '/' . $localpath
		);
	}

	public function pjActionIndex()
	{
		pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep0&install=1");
	}

	private static function pjActionCheckConfig($redirect=true)
	{
		$filename = 'app/config/config.inc.php';
		$content = @file_get_contents($filename);
		if (strpos($content, 'PJ_HOST') === false && strpos($content, 'PJ_INSTALL_URL') === false)
		{
			//Continue with installation
			return true;
		} else {
			if ($redirect)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep0&install=1");
			}
			return false;
		}
	}
	
	private function pjActionCheckSession()
	{
		if (!isset($_SESSION[$this->defaultInstaller]))
		{
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep1&install=1");
		}
	}
	
	private function pjActionCheckTables($link)
	{
		ob_start();
		readfile('app/config/database.sql');
		$string = ob_get_contents();
		ob_end_clean();

		preg_match_all('/DROP\s+TABLE(\s+IF\s+EXISTS)?\s+`(\w+)`/i', $string, $match);
		if (count($match[0]) > 0)
		{
			$arr = array();
			foreach ($match[2] as $k => $table)
			{
				$r = mysql_query(sprintf("SHOW TABLES FROM `%s` LIKE '%s'",
					$_SESSION[$this->defaultInstaller]['database'],
					$_SESSION[$this->defaultInstaller]['prefix'] . $table
				), $link);
				if (mysql_num_rows($r) > 0)
				{
					$row = mysql_fetch_assoc($r);
					$row = array_values($row);
					$arr[] = $row[0];
				}
			}
			return count($arr) === 0;
		}
		return true;
	}
	
	public function pjActionStep0()
	{
		if (self::pjActionCheckConfig(false))
		{
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep1&install=1");
		}
	}
	
	public function pjActionStep1()
	{
		self::pjActionCheckConfig();
		
		if (!isset($_SESSION[$this->defaultInstaller]))
		{
			$_SESSION[$this->defaultInstaller] = array();
		}
		if (!isset($_SESSION[$this->defaultErrors]))
		{
			$_SESSION[$this->defaultErrors] = array();
		}
		
		ob_start();
		phpinfo(INFO_MODULES);
		$content = ob_get_contents();
		ob_end_clean();
		
		// MySQL version -------------------
		if (!PJ_DISABLE_MYSQL_CHECK)
		{
			$mysql_version = NULL;
			$mysql_content = explode('name="module_mysql"', $content);
			if (count($mysql_content) > 1)
			{
				$mysql_content = explode("Client API version", $mysql_content[1]);
				if (count($mysql_content) > 1)
				{
					preg_match('/<td class="v">(.*)<\/td>/', $mysql_content[1], $m);
					if (count($m) > 0)
					{
						$mysql_version = trim($m[1]);
						
						if (preg_match('/(\d+\.\d+\.\d+)/', $mysql_version, $m))
						{
							$mysql_version = $m[1];
						}
					}
				}
			}
			$mysql_check = true;
			if (is_null($mysql_version) || version_compare($mysql_version, '4.1.0', '<'))
			{
				$mysql_check = false;
			}
			$this->set('mysql_check', $mysql_check);
		}
		
		// PHP version -------------------
		$php_check = true;
		if (version_compare(phpversion(), '5.0.0', '<'))
		{
			$php_check = false;
		}
		$this->set('php_check', $php_check);
				
		$filename = 'app/config/config.inc.php';
		$err_arr = array();
		if (!is_writable($filename))
		{
		    $err_arr[] = array('file', $filename, 'You need to set write permissions (chmod 777) to options file located at');
		}

		$folders = array();
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$err_arr[] = array('folder', $dir, 'You need to set write permissions (chmod 777) to directory located at');
			}
		}
		
		$this->set('folder_check', count($err_arr) === 0);
		$this->set('folder_arr', $err_arr);
			
		$this->appendJs('jquery.validate.min.js', $this->getConst('PLUGIN_LIBS_PATH'));
		$this->appendJs('pjInstaller.js', $this->getConst('PLUGIN_JS_PATH'));
	}

	public function pjActionStep2()
	{
		self::pjActionCheckConfig();
		
		$this->pjActionCheckSession();
		
		if (isset($_POST['step1']))
		{
			$_SESSION[$this->defaultInstaller] = array_merge($_SESSION[$this->defaultInstaller], $_POST);
		}
		
		if (!isset($_SESSION[$this->defaultInstaller]['step1']))
		{
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep1&install=1");
		}
		
		$this->appendJs('jquery.validate.min.js', $this->getConst('PLUGIN_LIBS_PATH'));
		$this->appendJs('pjInstaller.js', $this->getConst('PLUGIN_JS_PATH'));
	}
	
	public function pjActionStep3()
	{
		self::pjActionCheckConfig();
		
		$this->pjActionCheckSession();
		
		if (isset($_POST['step2']))
		{
			$_POST = pjSanitize::clean($_POST, array('encode' => false));
			$_SESSION[$this->defaultInstaller] = array_merge($_SESSION[$this->defaultInstaller], $_POST);
			
			$err = NULL;
			
			if (!isset($_POST['hostname']) || !isset($_POST['username']) || !isset($_POST['database']) ||
				!pjValidation::pjActionNotEmpty($_POST['hostname']) ||
				!pjValidation::pjActionNotEmpty($_POST['username']) ||
				!pjValidation::pjActionNotEmpty($_POST['database']))
			{
				$err = "Hostname, Username and Database are required and can't be empty.";
			} else {
				$link = @mysql_connect($_POST['hostname'], $_POST['username'], $_POST['password']);
				if (!$link)
				{
				    $err = mysql_error();
				} else {
					$db_selected = mysql_select_db($_POST['database'], $link);
					if (!$db_selected)
					{
					    $err = mysql_error($link);
					} else {
						if (!$this->pjActionCheckTables($link))
						{
							$this->set('warning', 1);
						}
						
						$tempTable = 'stivasoft_temp_install';
						
						mysql_query("DROP TABLE IF EXISTS `$tempTable`;", $link);
						
						if (!mysql_query("CREATE TABLE IF NOT EXISTS `$tempTable` (`created` datetime DEFAULT NULL);", $link))
						{
							$err .= "CREATE command denied to current user<br />";
						} else {
							if (!mysql_query("INSERT INTO `$tempTable` (`created`) VALUES (NOW());", $link))
							{
								$err .= "INSERT command denied to current user<br />";
							}
							if (!mysql_query("SELECT * FROM `$tempTable` WHERE 1=1;", $link))
							{
								$err .= "SELECT command denied to current user<br />";
							}
							if (!mysql_query("UPDATE `$tempTable` SET `created` = NOW();", $link))
							{
								$err .= "UPDATE command denied to current user<br />";
							}
							if (!mysql_query("DELETE FROM `$tempTable` WHERE 1=1;", $link))
							{
								$err .= "DELETE command denied to current user<br />";
							}
						}
						if (!mysql_query("DROP TABLE IF EXISTS `$tempTable`;", $link))
						{
							$err .= "DROP command denied to current user<br />";
						}
					}
				}
			}
			if (!is_null($err))
			{
				$time = time();
				$_SESSION[$this->defaultErrors][$time] = $err;
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep2&install=1&err=" . $time);
			}
			
			$this->set('paths', self::pjActionGetPaths());
			
			$this->set('status', 'ok');
		}
		
		if (!isset($_SESSION[$this->defaultInstaller]['step2']))
		{
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep2&install=1");
		}
		
		/* else if (isset($_SESSION[$this->defaultInstaller])) {
			$this->set('status', 'ok');
		}*/
		$this->appendJs('jquery.validate.min.js', $this->getConst('PLUGIN_LIBS_PATH'));
		$this->appendJs('pjInstaller.js', $this->getConst('PLUGIN_JS_PATH'));
	}

	public function pjActionStep4()
	{
		self::pjActionCheckConfig();
		
		$this->pjActionCheckSession();
		
		if (isset($_POST['step3']))
		{
			if (!isset($_POST['install_folder']) || !isset($_POST['install_url']) || !isset($_POST['install_path']) ||
				!pjValidation::pjActionNotEmpty($_POST['install_folder']) ||
				!pjValidation::pjActionNotEmpty($_POST['install_url']) ||
				!pjValidation::pjActionNotEmpty($_POST['install_path']))
			{
				$time = time();
				$_SESSION[$this->defaultErrors][$time] = "Folder Name, Full URL and Server Path are required and can't be empty.";
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep3&install=1&err=" . $time);
			} else {
				$_POST = pjSanitize::clean($_POST, array('encode' => false));
				$_SESSION[$this->defaultInstaller] = array_merge($_SESSION[$this->defaultInstaller], $_POST);
			}
		}
		
		if (!isset($_SESSION[$this->defaultInstaller]['step3']))
		{
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep3&install=1");
		}
		
		$this->appendJs('jquery.validate.min.js', $this->getConst('PLUGIN_LIBS_PATH'));
		$this->appendJs('pjInstaller.js', $this->getConst('PLUGIN_JS_PATH'));
	}
	
	public function pjActionStep5()
	{
		self::pjActionCheckConfig();
		
		$this->pjActionCheckSession();
		
		if (isset($_POST['step4']))
		{
			if (!isset($_POST['admin_email']) || !isset($_POST['admin_password']) ||
				!pjValidation::pjActionNotEmpty($_POST['admin_email']) ||
				!pjValidation::pjActionEmail($_POST['admin_email']) ||
				!pjValidation::pjActionNotEmpty($_POST['admin_password']))
			{
				$time = time();
				$_SESSION[$this->defaultErrors][$time] = "E-Mail and Password are required and can't be empty.";
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep4&install=1&err=" . $time);
			} else {
				$_POST = pjSanitize::clean($_POST, array('encode' => false));
				$_SESSION[$this->defaultInstaller] = array_merge($_SESSION[$this->defaultInstaller], $_POST);
			}
		}
		
		if (!isset($_SESSION[$this->defaultInstaller]['step4']))
		{
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep4&install=1");
		}
		
		$this->appendJs('jquery.validate.min.js', $this->getConst('PLUGIN_LIBS_PATH'));
		$this->appendJs('pjInstaller.js', $this->getConst('PLUGIN_JS_PATH'));
	}
	
	public function pjActionStep6()
	{
		self::pjActionCheckConfig();
		
		$this->pjActionCheckSession();
		
		if (isset($_POST['step5']))
		{
			if (!isset($_POST['license_key']) || !pjValidation::pjActionNotEmpty($_POST['license_key']))
			{
				$time = time();
				$_SESSION[$this->defaultErrors][$time] = "License Key is required and can't be empty.";
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep5&install=1&err=" . $time);
			} else {
				$_POST = pjSanitize::clean($_POST, array('encode' => false));
				$_SESSION[$this->defaultInstaller] = array_merge($_SESSION[$this->defaultInstaller], $_POST);
				
				$Http = new pjHttp();
				$Http->request(base64_decode("aHR0cDovL3N1cHBvcnQuc3RpdmFzb2Z0LmNvbS8=") . 'index.php?controller=Api&action=newInstall&key=' . urlencode($_POST['license_key']) .
					"&version=". urlencode(PJ_SCRIPT_VERSION) ."&script_id=" . urlencode(PJ_SCRIPT_ID) .
					"&server_name=" . urlencode($_SERVER['SERVER_NAME']) . "&ip=" . urlencode($_SERVER['REMOTE_ADDR']) .
					"&referer=" . urlencode($_SERVER['HTTP_REFERER']));
				$resp = $Http->getResponse();
				$output = unserialize($resp);
				if (isset($output['hash']) && isset($output['code']) && $output['code'] == 200)
				{
					$_SESSION[$this->defaultInstaller]['private_key'] = $output['hash'];
				} else {
					$time = time();
					$_SESSION[$this->defaultErrors][$time] = "Key is wrong or not valid. Please check you data again.";
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep5&install=1&err=" . $time);
				}
			}
		}
		
		if (!isset($_SESSION[$this->defaultInstaller]['step5']))
		{
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep5&install=1");
		}
		
		$this->appendJs('jquery.validate.min.js', $this->getConst('PLUGIN_LIBS_PATH'));
		$this->appendJs('pjInstaller.js', $this->getConst('PLUGIN_JS_PATH'));
	}
	
	public function pjActionStep7()
	{
		$this->pjActionCheckSession();
		
		if (isset($_POST['step6']))
		{
			$_POST = pjSanitize::clean($_POST, array('encode' => false));
			$_SESSION[$this->defaultInstaller] = array_merge($_SESSION[$this->defaultInstaller], $_POST);
		}
		
		if (!isset($_SESSION[$this->defaultInstaller]['step6']))
		{
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjInstaller&action=pjActionStep6&install=1");
		}
		
		unset($_SESSION[$this->defaultInstaller]);
		unset($_SESSION[$this->defaultErrors]);
	}
	
	public function pjActionSetDb()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$resp = array();
			$link = mysql_connect($_SESSION[$this->defaultInstaller]['hostname'], $_SESSION[$this->defaultInstaller]['username'], $_SESSION[$this->defaultInstaller]['password']);
			if (!$link)
			{
			    $resp['code'] = 100;
			    $resp['text'] = 'Could not connect: ' . mysql_error();
			} else {
				mysql_query("SET NAMES 'utf8'", $link);
				$db_selected = mysql_select_db($_SESSION[$this->defaultInstaller]['database'], $link);
				if (!$db_selected)
				{
				    $resp['code'] = 101;
				    $resp['text'] = mysql_error($link);
				} else {
					$idb = self::pjActionImportSQL('app/config/database.sql', $_SESSION[$this->defaultInstaller]['prefix']);
					if ($idb === true)
					{
						$_GET['install'] = 2;
						require 'app/config/options.inc.php';
						
						$result = $this->requestAction(array(
							'controller' => 'pjAppController',
							'action' => 'pjActionBeforeInstall'
						), array('return'));
						
						if (isset($CONFIG['plugins']))
						{
							if (is_array($CONFIG['plugins']))
							{
								foreach ($CONFIG['plugins'] as $plugin)
								{
									$file = PJ_PLUGINS_PATH . $plugin . '/config/database.sql';
									if (is_file($file))
									{
										$pdb = self::pjActionImportSQL($file, $_SESSION[$this->defaultInstaller]['prefix'], PJ_SCRIPT_PREFIX);
										if ($pdb === false) {
											$resp['code'] = 102;
											$resp['text'] = "File not found (or can't be read)";
										} elseif ($pdb === true) {
											
										} else {
											$resp['code'] = 103;
											$resp['text'] = $pdb;
										}
									}
									$model = $modelName = pjObject::getConstant($plugin, 'PLUGIN_MODEL');
									if (substr($model, -5) === 'Model')
									{
										$model = substr($model, 0, -5);
									}
									pjObject::import('Model', "$plugin:$model");
									if (class_exists($modelName) && method_exists($modelName, 'pjActionSetup'))
									{
										$pluginModel = new $modelName;
										$pluginModel->pjActionSetup();
									}

									$result = $this->requestAction(array(
										'controller' => $plugin,
										'action' => 'pjActionBeforeInstall'
									), array('return'));
									
									if ($result !== NULL && isset($result['code']) && $result['code'] != 200 && isset($result['info']))
									{
										$resp['text'] = join("<br>", $result['info']);
										$resp['code'] = 104;
									}
								}
							} elseif (is_scalar($CONFIG['plugins'])) {
								$plugin = $CONFIG['plugins'];
								$file = PJ_PLUGINS_PATH . $plugin . '/config/database.sql';
								{
									$pdb = self::pjActionImportSQL($file, $_SESSION[$this->defaultInstaller]['prefix'], PJ_SCRIPT_PREFIX);
									if ($pdb === false) {
										$resp['code'] = 102;
										$resp['text'] = "File not found (or can't be read)";
									} elseif ($pdb === true) {
										
									} else {
										$resp['code'] = 103;
										$resp['text'] = $pdb;
									}
								}
								$model = $modelName = pjObject::getConstant($plugin, 'PLUGIN_MODEL');
								if (substr($model, -5) === 'Model')
								{
									$model = substr($model, 0, -5);
								}
								pjObject::import('Model', "$plugin:$model");
								if (class_exists($modelName) && method_exists($modelName, 'pjActionSetup'))
								{
									$pluginModel = new $modelName;
									$pluginModel->pjActionSetup();
								}
								
								$result = $this->requestAction(array(
									'controller' => $plugin,
									'action' => 'pjActionBeforeInstall'
								), array('return'));
								
								if ($result !== NULL && isset($result['code']) && $result['code'] != 200 && isset($result['info']))
								{
									$resp['text'] = join("<br>", $result['info']);
									$resp['code'] = 104;
								}
							}
						}

						pjUserModel::factory()
							->setPrefix($_SESSION[$this->defaultInstaller]['prefix'])
							->setAttributes(array(
								'email' => $_SESSION[$this->defaultInstaller]['admin_email'],
								'password' => $_SESSION[$this->defaultInstaller]['admin_password'],
								'role_id' => 1,
								'name' => "Administrator",
								'ip' => $_SERVER['REMOTE_ADDR']
							))
							->insert();
						
						pjOptionModel::factory()
							->setPrefix($_SESSION[$this->defaultInstaller]['prefix'])
							->setAttributes(array(
								'foreign_id' => $this->getForeignId(),
								'key' => 'private_key',
								'tab_id' => 99,
								'value' => $_SESSION[$this->defaultInstaller]['private_key'],
								'type' => 'string'
							))
							->insert();
						
						if (!isset($resp['code']))
						{
							$resp['code'] = 200;
						}
					} elseif ($idb === false) {
						$resp['code'] = 102; //File not found (can't be open/read)
						$resp['text'] = "File not found (or can't be read)";
					} else {
						$resp['code'] = 103; //MySQL error
						$resp['text'] = $idb;
					}
				}
			}
			
			if (isset($resp['code']) && $resp['code'] != 200)
			{
				@file_put_contents('app/config/config.inc.php', '');
			}
			pjAppController::jsonResponse($resp);
		}
		exit;
	}
	
	public function pjActionSetConfig()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$resp = array();
			
			$sample = 'app/config/config.sample.php';
			$filename = 'app/config/config.inc.php';
			ob_start();
			readfile($sample);
			$string = ob_get_contents();
			ob_end_clean();
			if ($string === FALSE)
			{
				$resp['code'] = 100;
				$resp['text'] = "An error occurs while reading 'app/config/config.sample.php'";
			} else {
				$paths = self::pjActionGetPaths();
					
				$string = str_replace('[hostname]', $_SESSION[$this->defaultInstaller]['hostname'], $string);
				$string = str_replace('[username]', $_SESSION[$this->defaultInstaller]['username'], $string);
				$string = str_replace('[password]', $_SESSION[$this->defaultInstaller]['password'], $string);
				$string = str_replace('[database]', $_SESSION[$this->defaultInstaller]['database'], $string);
				$string = str_replace('[prefix]', $_SESSION[$this->defaultInstaller]['prefix'], $string);
				$string = str_replace('[install_folder]', $paths['install_folder'], $string);
				$string = str_replace('[install_path]', $paths['install_path'], $string);
				$string = str_replace('[install_url]', $paths['install_url'], $string);
				$string = str_replace('[salt]', pjUtil::getRandomPassword(8), $string);
					
				$Http = new pjHttp();
				$Http->request(base64_decode("aHR0cDovL3N1cHBvcnQuc3RpdmFzb2Z0LmNvbS8=") . 'index.php?controller=Api&action=getInstall'.
					"&key=" . urlencode($_SESSION[$this->defaultInstaller]['license_key']) .
					"&modulo=". urlencode(PJ_RSA_MODULO) .
					"&private=" . urlencode(PJ_RSA_PRIVATE) .
					"&server_name=" . urlencode($_SERVER['SERVER_NAME']));
				$response = $Http->getResponse();
				$output = unserialize($response);
				
				if (isset($output['hash']) && isset($output['code']) && $output['code'] == 200)
				{
					$string = str_replace('[pj_installation]', $output['hash'], $string);
				
					if (is_writable($filename))
					{
					    if (!$handle = @fopen($filename, 'wb'))
					    {
							$resp['code'] = 103;
							$resp['text'] = "'app/config/config.inc.php' open fails";
					    } else {
						    if (fwrite($handle, $string) === FALSE)
						    {
								$resp['code'] = 102;
								$resp['text'] = "An error occurs while writing to 'app/config/config.inc.php'";
						    } else {
					    		fclose($handle);
					    		$resp['code'] = 200;
						    }
					    }
					} else {
						$resp['code'] = 101;
						$resp['text'] = "'app/config/config.inc.php' do not exists or not writable";
					}
				} else {
					$resp['code'] = 104;
					$resp['text'] = "Security vulnerability detected";
				}
			}
			pjAppController::jsonResponse($resp);
		}
		exit;
	}
	
	public function pjActionLicense()
	{
		$arr = pjOptionModel::factory()
			->where('t1.foreign_id', $this->getForeignId())
			->where('t1.key', 'private_key')
			->limit(1)
			->findAll()
			->getData();

		$hash = NULL;
		if (count($arr) === 1)
		{
			$hash = $arr[0]['value'];
		}
		pjUtil::redirect(base64_decode("aHR0cDovL3N1cHBvcnQuc3RpdmFzb2Z0LmNvbS9jaGVja2xpY2Vuc2Uv") . $hash);
	}

	public function pjActionVersion()
	{
		if ($this->isLoged())
		{
			printf('PJ_SCRIPT_ID: %s<br>', PJ_SCRIPT_ID);
			printf('PJ_SCRIPT_BUILD: %s<br><br>', PJ_SCRIPT_BUILD);
			
			$plugins = pjRegistry::getInstance()->get('plugins');
			foreach ($plugins as $plugin => $whtvr)
			{
				printf("%s: %s<br>", $plugin, pjObject::getConstant($plugin, 'PLUGIN_BUILD'));
			}
		}
		exit;
	}
	
	public function pjActionHash()
	{
		@set_time_limit(0);
		
		if (!function_exists('md5_file'))
		{
			die("Function <b>md5_file</b> doesn't exists");
		}
		
		require 'app/config/config.inc.php';
		
		# Origin hash -------------
		if (!is_file(PJ_CONFIG_PATH . 'files.check'))
		{
			die("File <b>files.check</b> is missing");
		}
		$json = @file_get_contents(PJ_CONFIG_PATH . 'files.check');
		$Services_JSON = new pjServices_JSON();
		$data = $Services_JSON->decode($json);
		if (is_null($data))
		{
			die("File <b>files.check</b> is empty or broken");
		}
		$origin = get_object_vars($data);
				
		# Current hash ------------
		$data = array();
		pjUtil::readDir($data, PJ_INSTALL_PATH);
		$current = array();
		foreach ($data as $file)
		{
			$current[str_replace(PJ_INSTALL_PATH, '', $file)] = md5_file($file);
		}
		
		$html = '<style type="text/css">
		table{border: solid 1px #000; border-collapse: collapse; font-family: Verdana, Arial, sans-serif; font-size: 14px}
		td{border: solid 1px #000; padding: 3px 5px; background-color: #fff; color: #000}
		.diff{background-color: #0066FF; color: #fff}
		.miss{background-color: #CC0000; color: #fff}
		</style>
		<table cellpadding="0" cellspacing="0">
		<tr><td><strong>Filename</strong></td><td><strong>Status</strong></td></tr>
		';
		foreach ($origin as $file => $hash)
		{
			if (isset($current[$file]))
			{
				if ($current[$file] == $hash)
				{
					
				} else {
					$html .= '<tr><td>'. $file . '</td><td class="diff">changed</td></tr>';
				}
			} else {
				$html .= '<tr><td>'. $file . '</td><td class="miss">missing</td></tr>';
			}
		}
		$html .= '<table>';
		echo $html;
		exit;
	}
}
?>