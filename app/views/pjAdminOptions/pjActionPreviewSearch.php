<?php
ob_start();
?>
<!doctype html>
<html>
	<head>
		<title>Vacation Rental Listing by PHPJabbers.com</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=8" />
	</head>
	<body>
	{VRL_SEARCH}
	</body>
</html>

<?php
if (!isset($_GET['iframe']))
{
	$content = ob_get_contents();
	ob_end_clean();
	ob_start();
}

$controller->requestAction(array('controller' => 'pjListings', 'action' => 'pjActionSearch', 'params' => array('menu' => false)));

if (!isset($_GET['iframe']))
{
	$app = ob_get_contents();
	ob_end_clean();
	$app = str_replace('$','&#36;',$app);
	echo preg_replace('/\{VRL_SEARCH\}/', $app, $content);
}
?>