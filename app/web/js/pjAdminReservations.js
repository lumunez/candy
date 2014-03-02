var jQuery_1_8_2 = $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateReservation = $("#frmCreateReservation"),
			$frmUpdateReservation = $("#frmUpdateReservation"),
			$dialogMessage = $("#dialogMessage"),
			validate = ($.fn.validate !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			dialog = ($.fn.dialog !== undefined),
			chosen = ($.fn.chosen !== undefined);

		if (validate) {
			$.validator.addMethod("validDates", function (value, element) {
				return parseInt(value, 10) === 1; 
			}, myLabel.dateRangeValidation);
			
			$.validator.addMethod("numDays", function (value, element) {
				return parseInt(value, 10) === 1; 
			}, myLabel.numDaysValidation);
		}
		
		if ($frmCreateReservation.length > 0 && validate) {
			$frmCreateReservation.validate({
				rules: {
					"dates": "validDates",
					"days": "numDays"
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ".ignore"
			});
		}
		if ($frmUpdateReservation.length > 0 && validate) {
			$frmUpdateReservation.validate({
				rules: {
					"dates": "validDates",
					"days": "numDays"
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ".ignore"
			});
			
			$frmUpdateReservation.bind("submit.custom", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (dialog && $dialogMessage.length > 0) {
					$dialogMessage.dialog("open");
				}
				return false;
			});
			
			if ($dialogMessage.length > 0 && dialog) {
				var buttons = {};
				buttons[myLabel.btn_continue] = function() {
					var $this = $(this);
					if ($this.find("#dialog_confirm").is(":checked")) {
						var qs = ["&message=", $this.find("textarea").eq(0).val(), "&subject=", $this.find("input[type='text']").eq(0).val()].join("");
						$.post("index.php?controller=pjAdminReservations&action=pjActionSendMessage", $frmUpdateReservation.serialize() + qs).done(function (data) {
							$frmUpdateReservation.unbind(".custom").submit();
							$this.dialog('close');
						});
					} else {
						$frmUpdateReservation.unbind(".custom").submit();
						$this.dialog('close');
					}
				};
				buttons[myLabel.btn_cancel] = function() {
					$(this).dialog('close');
				};
				
				$dialogMessage.dialog({
					autoOpen: false,
					resizable: false,
					draggable: false,
					modal: true,
					width: 510,					
					open: function () {
						$.post("index.php?controller=pjAdminReservations&action=pjActionGetMessage", $frmUpdateReservation.serialize()).done(function (data) {
							$dialogMessage
								.find("textarea").text(data.body)
								.end()
								.find("input[type='text']").val(data.subject);
						});
					},
					close: function () {
						$dialogMessage.find("textarea").text("").end().find("input[type='text']").val("");
					},
					buttons: buttons
				});
			}
		}

		function checkDays($form) {
			$.post("index.php?controller=pjAdminReservations&action=pjActionCheckDays", $form.serialize()).done(function (data) {
				if (data.code === undefined) {
					return;
				}
				switch (data.code) {
				case 200:
					$("input#days").val('1');
					break;
				case 100:
					$("input#days").val('0');
					break;
				}
			});
		}
		
		function checkAvailability($form) {
			$.post("index.php?controller=pjAdminReservations&action=pjActionCheckAvailability", $form.serialize()).done(function (data) {
				if (data.code === undefined) {
					return;
				}
				switch (data.code) {
				case 200:
					$("input#dates").val('1');
					break;
				case 100:
					$("input#dates").val('0');
					break;
				}
			});
		}
		
		if ($frmCreateReservation.length > 0 || $frmUpdateReservation.length > 0) {
			var $date_from = $("#date_from");
			$date_from.datepicker({
				firstDay: $date_from.attr("rel"),
				dateFormat: $date_from.attr("rev"),
				onSelect: function (dateText, inst) {
					$("#date_to").datepicker("option", "minDate", dateText);
					
					var $form = $(this).closest("form");
					checkDays($form);
					checkAvailability($form);
				}
			});
			
			var $date_to = $("#date_to");
			$date_to.datepicker({
				firstDay: $date_to.attr("rel"),
				dateFormat: $date_to.attr("rev"),
				minDate: new Date(),
				onSelect: function (dateText, inst) {
					var $form = $(this).closest("form");
					checkDays($form);
					checkAvailability($form);
				}
			});
			
			$("#listing_id").bind("change", function (e) {
				var $form = $(this).closest("form");
				checkDays($form);
				checkAvailability($form);
			});
		}
		
		if (chosen) {
			$("#listing_id").chosen();
		}
		
		var $frmFilter = $(".frm-filter");
		if ($frmFilter.length > 0) {
			$frmFilter.on("change", "select[name='listing_id']", function (e) {
				$frmFilter.submit();	
			});
		}
		var $PM = $("#payment_method");
		if ($PM.length > 0) {
			$PM.bind("change", function () {
				if ($("option:selected", this).val() == 'creditcard') {
					$(".vrCC").show();
				} else {
					$(".vrCC").hide();
				}
			});	
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			function formatListing (val, obj) {
				return ['<a href="index.php?controller=pjAdminListings&action=pjActionUpdate&id=', obj.listing_id,'">', $.datagrid.wordwrap(val, 40, '<br>', true), '</a>'].join("");
			}
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminReservations&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminReservations&action=pjActionDeleteReservation&id={:id}"}
				          ],
				columns: [{text: myLabel.date_from, type: "date", sortable: true, editable: true,
								jqDateFormat: pjGrid.jqDateFormat,
								width: 100,
								editableWidth: 80, 
								renderer: $.datagrid._formatDate, 
								editableRenderer: $.datagrid._formatDate,
								dateFormat: pjGrid.jsDateFormat},
				          {text: myLabel.date_to, type: "date", sortable: true, editable: true, 
								jqDateFormat: pjGrid.jqDateFormat,
								width: 100,
								editableWidth: 80,
								renderer: $.datagrid._formatDate, 
								editableRenderer: $.datagrid._formatDate,
								dateFormat: pjGrid.jsDateFormat},
				          {text: myLabel.listing, type: "text", sortable: true, editable: false, renderer: formatListing, width: 280},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 90, options: [
				                                                                                     {label: myLabel.pending, value: "Pending"}, 
				                                                                                     {label: myLabel.confirmed, value: "Confirmed"},
				                                                                                     {label: myLabel.cancelled, value: "Cancelled"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminReservations&action=pjActionGetReservation" + pjGrid.queryString,
				dataType: "json",
				fields: ['date_from', 'date_to', 'listing_refid', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.export_selected, url: "index.php?controller=pjAdminReservations&action=pjActionExportReservation", ajax: false},
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminReservations&action=pjActionDeleteReservationBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminReservations&action=pjActionSaveReservation&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
			
			$(document).on("click", ".btn-all", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).addClass("pj-button-hover").siblings(".pj-button").removeClass("pj-button-hover");
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					status: ""
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "DESC", content.page, content.rowCount);
				return false;
			}).on("click", ".btn-confirmed", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).addClass("pj-button-hover").siblings(".pj-button").removeClass("pj-button-hover");
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					status: "Confirmed"
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "DESC", content.page, content.rowCount);
				return false;
			}).on("click", ".btn-pending", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).addClass("pj-button-hover").siblings(".pj-button").removeClass("pj-button-hover");
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					status: "Pending"
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "DESC", content.page, content.rowCount);
				return false;
			}).on("click", ".btn-cancelled", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).addClass("pj-button-hover").siblings(".pj-button").removeClass("pj-button-hover");
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					status: "Cancelled"
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "DESC", content.page, content.rowCount);
				return false;
			}).on("submit", ".frm-filter", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					listing_id: $this.find("option:selected", "select[name='listing_id']").val()
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "ASC", content.page, content.rowCount);
				return false;
			});
			
		}
		
		$(document).on("click", ".pj-form-field-icon-date", function (e) {
			$(this).parent().siblings("input[type='text']").datepicker("show");
		}).on("click change", "#dialog_confirm", function (e) {
			if ($(this).is(":checked")) {
				$dialogMessage.find("textarea").prop("readonly", false).removeClass("pj-form-field-readonly")
					.end().find("input[type='text']").prop("readonly", false).removeClass("pj-form-field-readonly");
			} else {
				$dialogMessage.find("textarea").prop("readonly", true).addClass("pj-form-field-readonly")
					.end().find("input[type='text']").prop("readonly", true).addClass("pj-form-field-readonly");
			}
		});
	});
})(jQuery_1_8_2);