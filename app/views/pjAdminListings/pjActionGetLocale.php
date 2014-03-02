<?php
$type = '<select name="type_id" id="type_id" class="pj-form-field w200 required">';
$type .= sprintf('<option value="">-- %s --</option>', __('lblChoose', true));
if (isset($tpl['type_arr']) && is_array($tpl['type_arr']))
{
	foreach ($tpl['type_arr'] as $v)
	{
		$type .= sprintf('<option value="%u">%s</option>', $v['id'], stripslashes($v['name']));
	}
}
$type .= '</select>';
// ----------------------
$status = '<select name="status" id="status" class="pj-form-field w200 required">';
$status .= sprintf('<option value="">-- %s --</option>', __('lblChoose', true));
$status .= sprintf('<option value="T">%s</option>', __('lblYes', true));
$status .= sprintf('<option value="F">%s</option>', __('lblNo', true));
$status .= '</select>';
// ----------------------
$featured = '<select name="is_featured" id="is_featured" class="pj-form-field w200 required">';
$featured .= sprintf('<option value="">-- %s --</option>', __('lblChoose', true));
$featured .= sprintf('<option value="T">%s</option>', __('lblYes', true));
$featured .= sprintf('<option value="F">%s</option>', __('lblNo', true));
$featured .= '</select>';

// ----------------------
pjAppController::jsonResponse(compact('type', 'status', 'featured'));
?>