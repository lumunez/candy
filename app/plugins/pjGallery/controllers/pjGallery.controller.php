<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once dirname(__FILE__) . '/pjGalleryAppController.controller.php';
class pjGallery extends pjGalleryAppController
{
	private $imageSizes = array(
		'small' => array(90, 68),
		'medium' => array(215, 161)
	);
	
	private $imageFiles = array('small_path', 'medium_path', 'large_path', 'source_path');
	
	private $imageCrop = true;
	
	private $imageFillColor = array(255, 255, 255); //RGB
	
	public function __construct()
	{
		parent::__construct();
		
		if (defined("PJ_GALLERY_SMALL") && strpos(PJ_GALLERY_SMALL, ",") !== FALSE)
		{
			$this->imageSizes['small'] = explode(",", preg_replace('/\s+/', '', PJ_GALLERY_SMALL));
		}
		if (defined("PJ_GALLERY_MEDIUM") && strpos(PJ_GALLERY_MEDIUM, ",") !== FALSE)
		{
			$this->imageSizes['medium'] = explode(",", preg_replace('/\s+/', '', PJ_GALLERY_MEDIUM));
		}
		if (defined("PJ_GALLERY_FILL_COLOR") && strpos(PJ_GALLERY_FILL_COLOR, ",") !== FALSE)
		{
			$this->imageFillColor = explode(",", preg_replace('/\s+/', '', PJ_GALLERY_FILL_COLOR));
		}
		if (defined("PJ_GALLERY_CROP"))
		{
			$this->imageCrop = (bool) PJ_GALLERY_CROP;
		}
	}
	
	private function pjActionDeleteImage($arr)
	{
		if (!is_array($arr))
		{
			$this->log('Given data is not an array');
			return FALSE;
		}
		foreach ($this->imageFiles as $file)
		{
			@clearstatcache();
			if (!empty($arr[$file]) && is_file($arr[$file]))
			{
				@unlink($arr[$file]);
			} else {
				$this->log(sprintf("%s is empty or not a file", $arr[$file]));
			}
		}
	}
	
	private function pjActionBuildFromSource(&$Image, $item, $watermark=NULL, $watermarkPosition="cc")
	{
		$data = array();
		if (empty($item['source_path']))
		{
			$this->log('source_path is empty');
			return FALSE;
		}
		foreach ($this->imageSizes as $key => $d)
		{
			if (isset($item[$key . '_path']) && !empty($item[$key . '_path']))
			{
				$dst = $item[$key . '_path'];
			} else {
				$dst = str_replace(PJ_UPLOAD_PATH . 'source/', PJ_UPLOAD_PATH . $key . '/', $item['source_path']);
			}
			$Image->loadImage($item['source_path']);
			if ($this->imageCrop)
			{
				$Image->setFillColor($this->imageFillColor)->resizeSmart($d[0], $d[1]);
			} else {
				$Image->resizeToWidth($d[0]);
			}
			if (!empty($watermark) && $key != 'small')
			{
				$Image->setWatermark($watermark, $watermarkPosition);
			}
			$Image->saveImage($dst);
			$data[$key . '_path'] = $dst;
			$data[$key . '_size'] = filesize($dst);
			$size = getimagesize($dst);
			$data[$key . '_width'] = $size[0];
			$data[$key . '_height'] = $size[1];
		}
		# Large image
		$dst = str_replace(PJ_UPLOAD_PATH . 'source/', PJ_UPLOAD_PATH . 'large/', $item['source_path']);
		$Image->loadImage($item['source_path']);
		if (!empty($watermark))
		{
			$Image->setWatermark($watermark, $watermarkPosition);
		}
		$Image->saveImage($dst);
		$data['large_path'] = $dst;
		$data['large_size'] = filesize($dst);
		$size = getimagesize($dst);
		$data['large_width'] = $size[0];
		$data['large_height'] = $size[1];
		return $data;
	}
	
	public function pjActionCompressGallery()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['foreign_id']) && (int) $_GET['foreign_id'] > 0)
			{
				pjObject::import('Model', 'pjGallery:pjGallery');
				$GalleryModel = pjGalleryModel::factory();
				$arr = $GalleryModel->where('t1.foreign_id', $_GET['foreign_id'])->findAll()->getData();
				if (count($arr) > 0)
				{
					$_POST['large_path_compression'] = $_POST['small_path_compression'];
					$_POST['medium_path_compression'] = $_POST['small_path_compression'];

					$Image = new pjImage();
					if ($Image->getErrorCode() !== 200)
					{
						foreach ($arr as $item)
						{
							$data = array();
							foreach ($this->imageFiles as $file)
							{
								if (!empty($item[$file]))
								{
									$compression = isset($_POST[$file.'_compression']) ? (int) $_POST[$file.'_compression'] : 60;
									$Image->loadImage($item[$file])->saveImage($item[$file], IMAGETYPE_JPEG, $compression);
									@clearstatcache();
									$data[str_replace('_path', '_size', $file)] = filesize($item[$file]);
								}
							}
							if (count($data) > 0)
							{
								$GalleryModel->reset()->setAttributes(array('id' => $item['id']))->modify($data);
							}
						}
					}
				} else {
					$this->log('No image records found in DB');
				}
			} else {
				$this->log("\$_GET['foreign_id'] is not set or has incorrect value");
			}
		}
		exit;
	}
	
	public function pjActionCropGallery()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				pjObject::import('Model', 'pjGallery:pjGallery');
				$GalleryModel = pjGalleryModel::factory();
				$arr = $GalleryModel->find($_POST['id'])->getData();
				if (count($arr) > 0)
				{
					$Image = new pjImage();
					if ($Image->getErrorCode() !== 200)
					{
						$Image->loadImage($arr[$_POST['src']]);
						if ($_POST['dst'] == 'large_path')
						{
							$Image->crop($_POST['x'], $_POST['y'], $_POST['w'], $_POST['h'], $_POST['w'], $_POST['h']);
						} else {
							$Image->crop(
								$_POST['x'],
								$_POST['y'],
								$this->imageSizes[str_replace('_path', '', $_POST['dst'])][0],
								$this->imageSizes[str_replace('_path', '', $_POST['dst'])][1],
								$_POST['w'],
								$_POST['h']
							);
						}
						$Image->saveImage($arr[$_POST['dst']]);
					} else {
						$this->log('GD is not loaded');
					}
					
					$key = str_replace('_path', '', $_POST['dst']);
					$data = array();
					$data[$key.'_size'] = filesize($arr[$_POST['dst']]);
					$size = @getimagesize($arr[$_POST['dst']]);
					if ($size !== false)
					{
						$data[$key.'_width'] = $size[0];
						$data[$key.'_height'] = $size[1];
					}
					$GalleryModel->reset()->where('id', $arr['id'])->limit(1)->modifyAll($data);
				} else {
					$this->log('Image record not found in DB');
				}
			} else {
				$this->log("\$_POST['id'] is not set or has incorrect value");
			}
		}
		exit;
	}
	
	public function pjActionEmptyGallery()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['foreign_id']) && (int) $_GET['foreign_id'] > 0)
			{
				pjObject::import('Model', 'pjGallery:pjGallery');
				$GalleryModel = pjGalleryModel::factory()->where('foreign_id', $_GET['foreign_id']);
				$arr = $GalleryModel->findAll()->getData();
				foreach ($arr as $item)
				{
					$this->pjActionDeleteImage($item);
				}
				$GalleryModel->eraseAll();
				$resp = array('code' => 200);
			} else {
				$resp = array('code' => 100);
				$this->log("\$_GET['foreign_id'] is not set or has incorrect value");
			}
			pjAppController::jsonResponse($resp);
		}
		exit;
	}
		
	public function pjActionDeleteGallery()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				pjObject::import('Model', 'pjGallery:pjGallery');
				
				$GalleryModel = pjGalleryModel::factory();
				$arr = $GalleryModel->find($_POST['id'])->getData();
				if (count($arr) > 0)
				{
					$this->pjActionDeleteImage($arr);
					$GalleryModel->erase();
					$resp = array('code' => 200);
				} else {
					$this->log("Image record not found in DB");
					$resp = array('code' => 101);
				}
			} else {
				$this->log("\$_POST['id'] is not set or has incorrect value");
				$resp = array('code' => 100);
			}
			pjAppController::jsonResponse($resp);
		}
		exit;
	}
	
	public function pjActionGetGallery()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			pjObject::import('Model', 'pjGallery:pjGallery');
			$pjGalleryModel = pjGalleryModel::factory();
			
			if (isset($_GET['foreign_id']) && (int) $_GET['foreign_id'] > 0)
			{
				$pjGalleryModel->where('t1.foreign_id', $_GET['foreign_id']);
			}
			
			$column = 'sort';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			$error = NULL;
			if (isset($_GET['error']))
			{
				$error = $_GET['error'];
			}

			$total = $pjGalleryModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 100;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjGalleryModel->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			$originals_size = $thumbs_size = 0;
			foreach ($data as $item)
			{
				$originals_size += (int) $item['source_size'];
				$thumbs_size += (int) $item['small_size'];
				$thumbs_size += (int) $item['medium_size'];
				$thumbs_size += (int) $item['large_size'];
			}
			pjAppController::jsonResponse(compact('data', 'originals_size', 'thumbs_size', 'total', 'pages', 'page', 'rowCount', 'column', 'direction', 'error'));
		}
		exit;
	}

	public function pjActionIndex()
	{
		$this->checkLogin();
	}

	public function pjActionRebuildGallery()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['foreign_id']) && (int) $_GET['foreign_id'] > 0)
			{
				$Image = new pjImage();
				if ($Image->getErrorCode() !== 200)
				{
					pjObject::import('Model', 'pjGallery:pjGallery');
					$GalleryModel = pjGalleryModel::factory();
	
					$arr = $GalleryModel->where('t1.foreign_id', $_GET['foreign_id'])->findAll()->getData();
					foreach ($arr as $item)
					{
						$data = array();
						$data = $this->pjActionBuildFromSource($Image, $item);
						$GalleryModel->reset()->where('id', $item['id'])->limit(1)->modifyAll($data);
					}
				} else {
					$this->log('GD extension is not loaded');
				}
			} else {
				$this->log("\$_GET['foreign_id'] is not set or has incorrect value");
			}
		}
		exit;
	}
	
	public function pjActionResizeGallery()
	{
		$this->checkLogin();
		
		pjObject::import('Model', 'pjGallery:pjGallery');
		$arr = pjGalleryModel::factory()->find($_GET['id'])->getData();
		if (count($arr) === 0)
		{
			pjUtil::redirect(sprintf("%sindex.php?controller=pjGallery&action=pjActionIndex&err=AG01", PJ_INSTALL_URL));
		}
		$this->set('arr', $arr);
		$this->set('imageSizes', $this->imageSizes);
		
		$this->appendJs('jquery.Jcrop.min.js', $this->getConst('PLUGIN_LIBS_PATH') . 'jcrop/js/');
		$this->appendCss('jquery.Jcrop.min.css', $this->getConst('PLUGIN_LIBS_PATH') . 'jcrop/css/');
		
		$this->appendJs('pjGallery.js', $this->getConst('PLUGIN_JS_PATH'));
	}
	
	public function pjActionRotateGallery()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				pjObject::import('Model', 'pjGallery:pjGallery');
				$arr = pjGalleryModel::factory()->find($_POST['id'])->getData();
				if (count($arr) > 0)
				{
					$Image = new pjImage();
					if ($Image->getErrorCode() !== 200)
					{
						if (!empty($arr['small_path']))
						{
							$Image->loadImage($arr['small_path'])->rotate()->saveImage($arr['small_path']);
						}
						if (!empty($arr['medium_path']))
						{
							$Image->loadImage($arr['medium_path'])->rotate()->saveImage($arr['medium_path']);
						}
						if (!empty($arr['large_path']))
						{
							$Image->loadImage($arr['large_path'])->rotate()->saveImage($arr['large_path']);
						}
					} else {
						$this->log('GD extesion is not loaded');
					}
				} else {
					$this->log("Image record not found in DB");
				}
			} else {
				$this->log("\$_POST['id'] is not set or has incorrect value");
			}
		}
		exit;
	}
	
	public function pjActionSortGallery()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['sort']) && is_array($_POST['sort']))
			{
				pjObject::import('Model', 'pjGallery:pjGallery');
				$GalleryModel = new pjGalleryModel();
				$arr = $GalleryModel->whereIn('id', $_POST['sort'])->orderBy("t1.sort ASC")->findAll()->getDataPair('id', 'sort');
				$fliped = array_flip($_POST['sort']);
				$combined = array_combine(array_keys($fliped), $arr);
				$GalleryModel->begin();
				foreach ($combined as $id => $sort)
				{
					$GalleryModel->setAttributes(compact('id'))->modify(compact('sort'));
				}
				$GalleryModel->commit();
			} else {
				$this->log("\$_POST['sort'] is not set or incorrect value");
			}
		}
		exit;
	}

	public function pjActionUpdateGallery()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			pjObject::import('Model', 'pjGallery:pjGallery');
			$GalleryModel = pjGalleryModel::factory();
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$arr = $GalleryModel->find($_POST['id'])->getData();
				if (count($arr) > 0)
				{
					$Image = new pjImage();
					if ($Image->getErrorCode() !== 200)
					{
						$Image->setFontSize(18)->setFont(PJ_WEB_PATH . 'obj/arialbd.ttf');
						
						$_POST['large_path_compression'] = $_POST['small_path_compression'];
						$_POST['medium_path_compression'] = $_POST['small_path_compression'];
								
						$data = array();
						foreach ($this->imageFiles as $file)
						{
							@clearstatcache();
							if (!empty($arr[$file]) && is_file($arr[$file]))
							{
								if (isset($_POST['watermark']) && !empty($_POST['watermark']) && $arr['watermark'] != $_POST['watermark'])
								{
									if ($file != 'source_path')
									{
										if (!empty($arr['watermark']))
										{
											// Init image, then set watermark
											if (!empty($arr[$file]))
											{
												$dst = $arr[$file];
											} else {
												$dst = str_replace(PJ_UPLOAD_PATH . 'source/', PJ_UPLOAD_PATH . str_replace('_path', '', $file) . '/', $arr['source_path']);
											}
											$Image->loadImage($arr['source_path']);
											if ($file != 'large_path')
											{
												if ($this->imageCrop)
												{
													$Image->setFillColor($this->imageFillColor)->resizeSmart($this->imageSizes[str_replace('_path', '', $file)][0], $this->imageSizes[str_replace('_path', '', $file)][1]);
												} else {
													$Image->resizeToWidth($this->imageSizes[str_replace('_path', '', $file)][0]);
												}
											}
											if ($file != 'small_path')
											{
												$Image->setWatermark($_POST['watermark'], $_POST['position']);
											}
											$Image->saveImage($dst);
										} else {
											if ($file != 'small_path')
											{
												$Image
													->loadImage($arr[$file])
													->setWatermark($_POST['watermark'], $_POST['position'])
													->saveImage($arr[$file]);
											}
										}
									}
								}
								# Compression ----------------
								if (!empty($arr[$file]))
								{
									$compression = isset($_POST[$file.'_compression']) ? (int) $_POST[$file.'_compression'] : 60;
									$Image->loadImage($arr[$file])->saveImage($arr[$file], IMAGETYPE_JPEG, $compression);
									@clearstatcache();
									$data[str_replace('_path', '_size', $file)] = filesize($arr[$file]);
								}
								# Compression ----------------
							}
						}
					
						if (empty($_POST['watermark']) && !empty($arr['watermark']))
						{
							// Clear watermark
							foreach ($this->imageSizes as $key => $d)
							{
								if (!empty($arr[$key . '_path']))
								{
									$dst = $arr[$key . '_path'];
								} else {
									$dst = str_replace(PJ_UPLOAD_PATH . 'source/', PJ_UPLOAD_PATH . $key . '/', $arr['source_path']);
								}
								$Image->loadImage($arr['source_path']);
								if ($this->imageCrop)
								{
									$Image->setFillColor($this->imageFillColor)->resizeSmart($d[0], $d[1]);
								} else {
									$Image->resizeToWidth($d[0]);
								}
								$Image->saveImage($dst);
								$data[$key . '_path'] = $dst;
							}
							# Large image
							$dst = str_replace(PJ_UPLOAD_PATH . 'source/', PJ_UPLOAD_PATH . 'large/', $arr['source_path']);
							$Image->loadImage($arr['source_path'])->saveImage($dst);
							$data['large_path'] = $dst;
						}
					} else {
						$this->log('GD extension is not loaded');
					}
					
					//alt & watermark
					$GalleryModel->modify(array_merge($_POST, $data));
				}
			} else {
				$arr = $GalleryModel->find($_GET['id'])->getData();
				
				pjAppController::jsonResponse($arr);
			}
		}
		exit;
	}

	public function pjActionUploadGallery()
	{
		$this->checkLogin();
		$this->setAjax(true);

		ini_set('post_max_size', '50M');
		ini_set('upload_max_filesize', '50M');
		
		$resp = array();
		
		$post_max_size = ini_get('post_max_size');
		switch (substr($post_max_size, -1))
		{
			case 'G':
				$post_max_size = (int) $post_max_size * 1024 * 1024 * 1024;
				break;
			case 'M':
				$post_max_size = (int) $post_max_size * 1024 * 1024;
				break;
			case 'K':
				$post_max_size = (int) $post_max_size * 1024;
				break;
		}
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
		{
			$error = 'Posted data is too large. '. $_SERVER['CONTENT_LENGTH'].' bytes exceeds the maximum size of '. $post_max_size.' bytes.';
			$this->log("The \$_SERVER['CONTENT_LENGTH'] exceeds the post_max_size directive in php.ini.");
			$this->set('error', $error);
		} else {
			if (isset($_FILES['image']))
			{
				$Image = new pjImage();
				if ($Image->getErrorCode() !== 200)
				{
					$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
				
					if ($Image->load($_FILES['image']))
					{
						$resp = $Image->isConvertPossible();
						if ($resp['status'] === true)
						{
							$hash = md5(uniqid(rand(), true));
							$source_path = PJ_UPLOAD_PATH . 'source/' . $_GET['foreign_id'] . '_' . $hash . '.' . $Image->getExtension();
							if ($Image->save($source_path))
							{
								pjObject::import('Model', 'pjGallery:pjGallery');
								$GalleryModel = pjGalleryModel::factory();
								
								$arr = $GalleryModel->where('t1.foreign_id', $_GET['foreign_id'])->orderBy('t1.sort DESC')->limit(1)->findAll()->getData();
								$sort = 1;
								if (count($arr) === 1)
								{
									$sort = (int) $arr[0]['sort'] + 1;
								}
								
								$data = array();
								$data['foreign_id'] = $_GET['foreign_id'];
								$data['mime_type'] = $_FILES['image']['type'];
								$data['source_path'] = $source_path;
								$data['source_size'] = $_FILES['image']['size'];
								$data['name'] = $_FILES['image']['name'];
								$data['sort'] = $sort;
								
								$data = array_merge($data, $this->pjActionBuildFromSource($Image, $data));
			
								$size = $Image->getImageSize();
								$data['source_width'] = $size[0];
								$data['source_height'] = $size[1];
								
								$GalleryModel->reset()->setAttributes($data)->insert();
							} else {
								$this->log('Image has not been saved');
							}
						} else {
							// Not enough memory
							// $resp['memory_needed']
							// $resp['memory_limit']
							$this->set('error', sprintf('Allowed memory size of %u bytes exhausted (tried to allocate %u bytes)', $resp['memory_limit'], $resp['memory_needed']));
							$this->log($this->get('error'));
						}
					} else {
						$this->set('error', $Image->getError());
						$this->log($this->get('error'));
					}
				} else {
					$this->log('GD extension is not loaded');
				}
			} else {
				$this->log("\$_FILES['image'] is not set");
				$this->set('error', 'Image is not set');
			}
		}
		if ($this->get('error') !== FALSE)
		{
			$resp['error'] = $this->get('error');
		}
		header("Content-Type: text/html; charset=utf-8"); //fix for IE
		echo pjAppController::jsonEncode($resp);
		exit;
	}

	public function pjActionWatermarkGallery()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['foreign_id']) && (int) $_GET['foreign_id'] > 0)
			{
				pjObject::import('Model', 'pjGallery:pjGallery');
				$GalleryModel = pjGalleryModel::factory();
				$arr = $GalleryModel->where('foreign_id', $_GET['foreign_id'])->findAll()->getData();
				if (count($arr) > 0)
				{
					$Image = new pjImage();
					if ($Image->getErrorCode() !== 200)
					{
						$Image->setFontSize(18)->setFont(PJ_WEB_PATH . 'obj/arialbd.ttf');
						foreach ($arr as $item)
						{
							if (isset($_POST['watermark']))
							{
								$this->pjActionBuildFromSource($Image, $item, $_POST['watermark'], $_POST['position']);
							} else {
								$this->pjActionBuildFromSource($Image, $item);
							}
						}
					} else {
						$this->log('GD extension is not loaded');
					}
					if (isset($_POST['watermark']))
					{
						$data = array('watermark' => $_POST['watermark']);
					} else {
						$data = array('watermark' => array('NULL'));
					}
					$GalleryModel->modifyAll($data);
				} else {
					$this->log('No image records found in DB');
				}
			} else {
				$this->log("\$_GET['foreign_id'] is not set or has incorrect value");
			}
		}
		exit;
	}
}
?>