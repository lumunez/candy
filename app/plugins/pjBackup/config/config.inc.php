<?php
$config = array();
$config['PLUGIN_NAME'] = 'pjBackup';
$config['PLUGIN_DIR'] = 'app/plugins/' . $config['PLUGIN_NAME'] . '/';
$config['PLUGIN_CONTROLLERS_PATH'] = $config['PLUGIN_DIR'] . 'controllers/';
$config['PLUGIN_MODELS_PATH'] = $config['PLUGIN_DIR'] . 'models/';
$config['PLUGIN_VIEWS_PATH'] = $config['PLUGIN_DIR'] . 'views/';
$config['PLUGIN_COMPONENTS_PATH'] = $config['PLUGIN_CONTROLLERS_PATH'] . 'components/';
$config['PLUGIN_WEB_PATH'] = $config['PLUGIN_DIR'] . 'web/';
$config['PLUGIN_IMG_PATH'] = $config['PLUGIN_WEB_PATH'] . 'img/';
$config['PLUGIN_CSS_PATH'] = $config['PLUGIN_WEB_PATH'] . 'css/';
$config['PLUGIN_JS_PATH'] = $config['PLUGIN_WEB_PATH'] . 'js/';

$config['PLUGIN_ID'] = "103";
$config['PLUGIN_VERSION'] = "0.9";
$config['PLUGIN_BUILD'] = "0.9.0";

$registry = pjRegistry::getInstance();
$registry->set($config['PLUGIN_NAME'], $config);
$plugins = $registry->get('plugins');
if (is_null($plugins))
{
	$plugins = array();
}
$plugins[$config['PLUGIN_NAME']] = array('pjBackup');
$registry->set('plugins', $plugins);

unset($config);
unset($plugins);
?>