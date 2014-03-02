var jQuery_1_8_2 = $.noConflict();
(function ($, undefined) {
	$(function () {
		var datagrid = ($.fn.datagrid !== undefined);
		
		if ($('#frmCreateType').length > 0) {
			$('#frmCreateType').validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		if ($('#frmUpdateType').length > 0) {
			$('#frmUpdateType').validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminTypes&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminTypes&action=pjActionDeleteType&id={:id}"}
				          ],
				columns: [{text: myLabel.type, type: "text", sortable: true, editable: true, width: 530},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminTypes&action=pjActionGetType",
				dataType: "json",
				fields: ['name', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminTypes&action=pjActionDeleteTypeBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminTypes&action=pjActionSaveType&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
	});
})(jQuery_1_8_2);