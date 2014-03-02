var jQuery_1_8_2 = $.noConflict();
(function ($, undefined) {
	$(function () {
		var datagrid = ($.fn.datagrid !== undefined);

		if ($('#frmCreateExtra').length > 0) {
			$('#frmCreateExtra').validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		if ($('#frmUpdateExtra').length > 0) {
			$('#frmUpdateExtra').validate({
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
				buttons: [{type: "edit", url: "index.php?controller=pjAdminExtras&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminExtras&action=pjActionDeleteExtra&id={:id}"}
				          ],
				columns: [{text: myLabel.extra, type: "text", sortable: true, editable: true, width: 350},
				          {text: myLabel.type, type: "select", sortable: true, editable: true, width: 110, editableWidth: 110, options: [{
				        	  label: myLabel.extra_property, value: "property"
				          }, {
				        	  label: myLabel.extra_community, value: "community"
				          }]},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, options: [{
				        	  label: myLabel.active, value: "T"
				          }, {
				        	  label: myLabel.inactive, value: "F"
				          }], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminExtras&action=pjActionGetExtra",
				dataType: "json",
				fields: ['name', 'type', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminExtras&action=pjActionDeleteExtraBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminExtras&action=pjActionSaveExtra&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
	});
})(jQuery_1_8_2);