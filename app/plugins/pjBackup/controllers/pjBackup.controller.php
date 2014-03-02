<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once dirname(__FILE__) . '/pjBackupAppController.controller.php';
class pjBackup extends pjBackupAppController
{
	public function pjActionDelete()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			$response = array();
			$file = 'app/web/backup/' . $_GET['id'];
			clearstatcache();
			if (is_file($file))
			{
				@unlink($file);
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				foreach ($_POST['record'] as $item)
				{
					$file = 'app/web/backup/' . $item;
					clearstatcache();
					if (is_file($file))
					{
						@unlink($file);
					}
				}
			}
		}
		exit;
	}
	
	public function pjActionDownload()
	{
		$this->setAjax(true);
		
		if ($this->isLoged() && $this->isAdmin())
		{
			if (isset($_GET['id']) && !empty($_GET['id']))
			{
				ob_start();
				$file = 'app/web/backup/'.$_GET['id'];
				@clearstatcache();
				if (is_file($file))
				{
					readfile($file);
				}
				$data = ob_get_contents();
				ob_end_clean();
				pjToolkit::download($data, $_GET['id']);
			}
		}
		exit;
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			$column = 'created';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$data = $id = $created = $type = array();
			if ($handle = opendir(PJ_WEB_PATH . 'backup'))
			{
				$i = 0;
				while (false !== ($entry = readdir($handle)))
				{
					preg_match('/(database-backup|files-backup)-(\d{10})\.(sql|zip)/', $entry, $m);
					if (isset($m[2]))
					{
						$id[$i] = $entry;
						$created[$i] = date($this->option_arr['o_date_format'] . ", H:i", $m[2]);
						$type[$i] = $m[1] == 'database-backup' ? 'database' : 'files';
						
						$data[$i]['id'] = $id[$i];
						$data[$i]['created'] = $created[$i];
						$data[$i]['type'] = $type[$i];
						$i++;
					}
				}
				closedir($handle);
			}
			
			switch ($column)
			{
				case 'created':
					array_multisort($created, $direction == 'ASC' ? SORT_ASC : SORT_DESC, $id, SORT_DESC, $type, SORT_ASC, $data);
					break;
				case 'type':
					array_multisort($type, $direction == 'ASC' ? SORT_ASC : SORT_DESC, $id, SORT_DESC, $created, SORT_DESC, $data);
					break;
				case 'id':
					array_multisort($id, $direction == 'ASC' ? SORT_ASC : SORT_DESC, $type, SORT_ASC, $created, SORT_DESC, $data);
					break;
			}
			
			$total = count($data);
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
						
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin())
		{
			$this->set('status', 2);
			return;
		}
		
		if (isset($_POST['backup']))
		{
			set_time_limit(600); //10 min
			$err = 'AB04';
			if (isset($_POST['db']))
			{
				$AppModel = pjAppModel::factory();
				$arr = $AppModel->prepare(sprintf("SHOW TABLES FROM `%s`", PJ_DB))->exec()->getData();
				
				$sql = array();
				
				foreach ($arr as $item)
				{
					$table = array_values($item);
					$table = $table[0];
					
					if (strpos($table, PJ_PREFIX . PJ_SCRIPT_PREFIX) !== 0)
					{
						continue;
					}

					$result = $AppModel->reset()->prepare(sprintf("SELECT * FROM `%s`", $table))->exec()->getData();
    				$sql[] = sprintf("DROP TABLE IF EXISTS `%s`;\n\n", $table);

    				$create = $AppModel->reset()->prepare(sprintf("SHOW CREATE TABLE `%s`", $table))->exec()->getData();
    				$create = array_values($create[0]);
    				$sql[] = sprintf("%s;\n\n", $create[1]);
    				
    				foreach ($result as $row)
    				{
    					$sql[] = sprintf("INSERT INTO `%s` VALUES(", $table);
    					$insert = array();
    					foreach ($row as $key => $val)
    					{
    						$val = str_replace('\n', '\r\n', $val);
    						$val = preg_replace("/\r\n/", '\r\n', $val);
    						$insert[] = "'" . str_replace("'", "''", $val) . "'";
    					}
    					$sql[] = join(", ", $insert);
    					$sql[] = ");\n";
    				}
    				$sql[] = "\n";
				}
    			$content = join("", $sql);
    			
    			if (!$handle = fopen(PJ_WEB_PATH . 'backup/database-backup-'.time().'.sql', 'wb'))
    			{
    			} else {
					if (fwrite($handle, $content) === FALSE)
					{
					} else {
						fclose($handle);
						$err = 'AB02';
					}
    			}
			}
			
			if (isset($_POST['files']))
			{
				$Zip = new pjZip();
				$Zip->zipDirectory(PJ_UPLOAD_PATH, '/');
				
				$zipName = PJ_WEB_PATH . 'backup/files-backup-'.time().'.zip';
				
				if (!$handle = fopen($zipName, "wb"))
				{
				} else {
					if (fwrite($handle, $Zip->getZippedfile()) === FALSE)
					{
					} else {
						fclose($handle);
						$err = 'AB02';
					}
				}
			}
			
			if (!isset($_POST['db']) && !isset($_POST['files']))
			{
				$err = 'AB03';
			}
			pjUtil::redirect(sprintf("%sindex.php?controller=pjBackup&action=pjActionIndex&err=%s", PJ_INSTALL_URL, $err));
		}
		
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('pjBackup.js', $this->getConst('PLUGIN_JS_PATH'));
	}
}
?>