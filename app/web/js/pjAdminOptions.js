var jQuery_1_8_2 = $.noConflict();
(function ($, undefined) {
	$(function () {
		var tabs = ($.fn.tabs !== undefined),
			dialog = ($.fn.dialog !== undefined),
			validate = ($.fn.validate !== undefined),
			spinner = ($.fn.spinner !== undefined),
			$dialogDeletePeriod = $("#dialogDeletePeriod"),
			$frmOptions = $("#frmOptions"),
			$fieldInt = $(".field-int"),
			$tabs = $("#tabs");
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs();
		}
		
		if ($frmOptions.length > 0 && validate) {
			$frmOptions.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if (spinner && $fieldInt.length > 0) {
			$fieldInt.spinner();
			$fieldInt.each(function () {
				var min = $(this).data("min");
				$(this).spinner("option", "min", min)
			});
		}
		
		$("#content").on("focusin", ".textarea_install", function (e) {
			$(this).select();
		}).on("change", "select[name='value-enum-o_send_email']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'mail|smtp::mail':
				$(".boxSmtp").hide();
				break;
			case 'mail|smtp::smtp':
				$(".boxSmtp").show();
				break;
			}
		}).on("click", ".btnAddPeriod", function () {
			var $c = $("#tblPeriodClone tbody").clone(),
				r = $c.html().replace(/\{INDEX\}/g, 'new_' + Math.ceil(Math.random() * 99999));
			$(this).closest("form").find("table").find("tbody").append(r);
		}).on("click", ".btnDeletePeriod", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($dialogDeletePeriod.length > 0 && dialog) {
				$dialogDeletePeriod.data("link", $(this)).dialog("open");
			}
			return false;
		}).on("click", ".btnRemovePeriod", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});			
			return false;
		});
		
		if ($dialogDeletePeriod.length > 0 && dialog) {
			var buttons = {};
			buttons[myLabel.btn_delete] = function () {
				var $this = $(this),
					$link = $this.data("link"),
					$tr = $link.closest("tr"),
					id = $link.data("id");
				
				$.post("index.php?controller=pjAdminOptions&action=pjActionDeletePeriod", {
					"id": id
				}).done(function (data) {
					if (data.code === undefined) {
						return;
					}
					switch (data.code) {
						case 200:
							$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
								$tr.remove();
								$this.dialog("close");
							});
							break;
					}
				});
			};
			buttons[myLabel.btn_cancel] = function () {
				$(this).dialog("close");
			};
			$dialogDeletePeriod.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				buttons: buttons
			});
		}
		
	});
})(jQuery_1_8_2);