<!doctype html>
<html>
	<head>
		<title>Install Wizard</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=9" />
		<?php
		foreach ($controller->getCss() as $css)
		{
			echo '<link type="text/css" rel="stylesheet" href="'.$css['path'].$css['file'].'" />';
		}
		
		foreach ($controller->getJs() as $js)
		{
			echo '<script src="'.$js['path'].$js['file'].'"></script>';
		}
		?>
	</head>
	<body>
		<div id="container">
    		<div id="header">
				<a href="http://www.phpjabbers.com/" id="logo" target="_blank"><img src="<?php echo pjObject::getConstant('pjInstaller', 'PLUGIN_IMG_PATH'); ?>install-logo.png" alt="Install Wizard" /></a>
			</div>
			<div id="middle">
			<?php require $content_tpl; ?>
			</div>
		</div>
	</body>
</html>