var jQuery_1_8_2 = $.noConflict();
(function ($, undefined) {
	$(function () {
		var $tabs = $("#tabs"),
			tabs = ($.fn.tabs !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs({
				select: function (event, ui) {
					switch (ui.index) {
						case 0:
							window.location.href = 'index.php?controller=pjAdminOptions&action=pjActionIndex&tab=0';
							break;
					}
				}
			});
		}
				
		if ($("#grid").length > 0 && datagrid) {
			
			function formatImage (str) {
				return (str && str.length > 0) ? '<img alt="" src="core/framework/libs/pj/img/flags/' + str + '" />' : '';
			}
			
			function formatDefault (str) {
				return '<a href="#" class="pj-status-icon pj-status-' + str + '" style="cursor: ' +  (parseInt(str, 10) === 0 ? 'pointer' : 'default') + '"></a>';
			}
			
			function onBeforeShow(obj) {
				if (parseInt(obj.is_default, 10) === 1) {
					return false;
				}
				return true;
			}

			var $grid = $("#grid").datagrid({
				buttons: [{type: "delete", url: "index.php?controller=pjAdminLocales&action=pjActionDeleteLocale&id={:id}", beforeShow: onBeforeShow}],
				columns: [{text: myLabel.title, type: "select", sortable: true, editable: true, width: 480, options: pjGrid.languages},
				          {text: myLabel.flag, type: "text", sortable: false, editable: false, width: 40, renderer: formatImage, align: "center"},
				          {text: myLabel.is_default, type: "text", sortable: true, editable: false, width: 80, renderer: formatDefault, align: "center"},
				          {text: myLabel.order, type: "text", sortable: true, editable: false, align: "center", width: 55, css: {
				        	  cursor: "move"
				          }}],
				dataUrl: "index.php?controller=pjAdminLocales&action=pjActionGetLocale",
				dataType: "json",
				fields: ['language_iso', 'file', 'is_default', 'sort'],
				paginator: false,
				saveUrl: "index.php?controller=pjAdminLocales&action=pjActionSaveLocale&id={:id}",
				sortable: true,
				sortableUrl: "index.php?controller=pjAdminLocales&action=pjActionSortLocale"
			});
			
			$(document).on("click", ".btn-add", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$.post("index.php?controller=pjAdminLocales&action=pjActionSaveLocale").done(function (data) {
					$grid.datagrid("option", "onRender", function () {
						$("tr[data-id='id_" + data.id + "']").find(".pj-table-cell-editable").filter(":first").trigger("click");
						$grid.datagrid("option", "onRender", null);
					});
					$grid.datagrid("load", "index.php?controller=pjAdminLocales&action=pjActionGetLocale");
				});
				return false;
			}).on("click", ".pj-status-1", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				return false;
			}).on("click", ".pj-status-0", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$.post("index.php?controller=pjAdminLocales&action=pjActionSaveDefault", {
					id: $(this).closest("tr").data("object")['id']
				}).done(function (data) {
					$grid.datagrid("load", "index.php?controller=pjAdminLocales&action=pjActionGetLocale");
				});
				return false;
			});
		}
	});
})(jQuery_1_8_2);