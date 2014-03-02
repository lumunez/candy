<select name="<?php echo $_GET['name']; ?>[]" multiple="multiple" size="5">
<?php
$forbidden = array();
if ((int) $_GET['role_id'] === 2)
{
	$forbidden = array(4,6);
}
if ((int) $_GET['role_id'] === 3)
{
	$forbidden = array(1,2,3,5);
}
foreach (__('notify_email', true) as $k => $v)
{
	if (in_array($k, $forbidden))
	{
		continue;
	}
	?><option value="<?php echo $k; ?>"<?php echo isset($tpl['arr']) && in_array($k, $tpl['arr']) ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
}
?>
</select>