<?php
if (!isset($_GET['iframe']))
{
	$content = ob_get_contents();
	ob_end_clean();
	ob_start();
}
if (!isset($_GET['controller']) || empty($_GET['controller']))
{
	$_GET["controller"] = "pjListings";
}
if (!isset($_GET['action']) || empty($_GET['action']))
{
	$_GET["action"] = "pjActionFeatured";
}
$_GET['params'] = array();
$_GET['params']['menu'] = false;
if (isset($VRL_SearchForm) && $VRL_SearchForm === true)
{
	$_GET['params']['search'] = true;
}
$dirname = str_replace("\\", "/", dirname(__FILE__));
include str_replace("app/views/pjLayouts", "", $dirname) . '/ind'.'ex.php';

$meta = NULL;
$meta_arr = $pjObserver->getController()->get('meta_arr');
if ($meta_arr !== FALSE)
{
	$meta = sprintf('<title>%s</title>
<meta name="keywords" content="%s" />
<meta name="description" content="%s" />',
		stripslashes($meta_arr['title']),
		htmlspecialchars(stripslashes($meta_arr['keywords'])),
		htmlspecialchars(stripslashes($meta_arr['description']))
	);
}
$content = str_replace('{VRL_META}', $meta, $content);

if (!isset($_GET['iframe']))
{
	$app = ob_get_contents();
	ob_end_clean();
	ob_start();
	$app = str_replace('$','&#36;',$app);
	echo preg_replace('/\{VRL_FEATURED\}/', $app, $content);
}
?>