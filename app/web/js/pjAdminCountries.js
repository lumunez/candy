var jQuery_1_8_2 = $.noConflict();
(function ($, undefined) {
	$(function () {
		var datagrid = ($.fn.datagrid !== undefined);

		if ($('#frmCreateCountry').length > 0) {
			$('#frmCreateCountry').validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		if ($('#frmUpdateCountry').length > 0) {
			$('#frmUpdateCountry').validate({
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
				buttons: [{type: "edit", url: "index.php?controller=pjAdminCountries&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminCountries&action=pjActionDeleteCountry&id={:id}"}
				          ],
				columns: [{text: myLabel.country, type: "text", sortable: true, editable: true, width: 530},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminCountries&action=pjActionGetCountry",
				dataType: "json",
				fields: ['name', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminCountries&action=pjActionDeleteCountryBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminCountries&action=pjActionSaveCountry&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
	});
})(jQuery_1_8_2);