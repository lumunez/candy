var jQuery_1_8_2 = $.noConflict();
(function ($, undefined) {
	$(function () {
		var datagrid = ($.fn.datagrid !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			gallery = ($.fn.gallery !== undefined),
			chosen = ($.fn.chosen !== undefined),
			dialog = ($.fn.dialog !== undefined),
			validate = ($.fn.validate !== undefined),
			tipsy = ($.fn.tipsy !== undefined),
			spinner = ($.fn.spinner !== undefined),
			$frmCreateListing = $("#frmCreateListing"),
			$frmUpdateListing = $("#frmUpdateListing"),
			$dialogDeletePrice = $("#dialogDeletePrice"),
			$gallery = $("#gallery");
		
		if ($("#tabs").length > 0) {
			$("#tabs").tabs({
				select: function (event, ui) {
					$(":input[name='tab_id']").val(ui.panel.id);
				}
			});
		}
		if ($frmCreateListing.length > 0 && validate) {
			$frmCreateListing.validate({
				rules: {
					"listing_refid": {
						required: true,
						remote: "index.php?controller=pjAdminListings&action=pjActionCheckRefId"
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				errorClass: "err",
				wrapper: "em",
				onkeyup: false,
				ignore: ".ignore"
			});
			
			if (chosen) {
				$("#owner_id").chosen();
			}
		}
		
		if (chosen) {
			$("#user_id").chosen();
		}
		
		if ($dialogDeletePrice.length > 0 && dialog) {
			$dialogDeletePrice.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				buttons: {
					"Delete": function () {
						var $this = $(this),
							$link = $this.data("link"),
							$tr = $link.closest("tr");
						$.post("index.php?controller=pjAdminListings&action=pjActionDeletePrice", {
							id: $link.data("id")
						}).done(function () {
							$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
								$tr.remove();
								$this.dialog("close");
							});
						});
					},
					"Cancel": function () {
						$(this).dialog("close");
					}
				}
			});
		}
				
		$("#content").on("click", ".btnDeletePrice", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($dialogDeletePrice.length > 0 && dialog) {
				$dialogDeletePrice.data('link', $(this)).dialog("open");
			}
			return false;
		}).on("click", ".btnRemovePrice", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
				$this.dialog("close");
			});
			return false;
		}).on("click", "input[name='o_allow_paypal']", function (e) {
			if ($(this).is(":checked")) {
				$(".PayPal").show();
				$(".PayPal input").addClass('email required');
			} else {
				$(".PayPal").hide();
				$(".PayPal input").removeClass('email required');
			}
		}).on("click", "input[name='o_allow_authorize']", function (e) {
			if ($(this).is(":checked")) {
				$(".AuthorizeNet").show();
				$(".AuthorizeNet input").addClass('required');
			} else {
				$(".AuthorizeNet").hide();
				$(".AuthorizeNet input").removeClass('required');
			}
		}).on("click", "input[name='o_allow_bank']", function (e) {
			if ($(this).is(":checked")) {
				$(".BankAccount").show();
				$(".BankAccount textarea").addClass('required');
			} else {
				$(".BankAccount").hide();
				$(".BankAccount textarea").removeClass('required');
			}
		}).on("click", "#btnAddPrice", function (e) {
			var $tr,
				$tbody = $("#tblPrices tbody"),
				h = $tbody.find("tr:last").find("td:first").html(),
				i = (h === null) ? 0 : parseInt(h, 10);
			i = !isNaN(i) ? i : 0;
			$tr = $("#tblPricesClone").find("tbody").clone();
			$tbody.find(".notFound").remove();
			$tbody.append($tr.html().replace(/\{INDEX\}/g, i + 1));
		}).on("focusin", ".datepick", function (e) {
			var $this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev")
			};
			switch ($this.attr("name")) {
			case "date_from[]":
				custom.maxDate = $this.closest("tr").find(".datepick[name='date_to[]']").datepicker({
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev")
				}).datepicker("getDate");
				break;
			case "date_to[]":
				custom.minDate = $this.closest("tr").find(".datepick[name='date_from[]']").datepicker({
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev")
				}).datepicker("getDate");
				break;
			}
			if ($this.hasClass("hasDatepicker")) {
				$this.datepicker("destroy");
			}
			$this.datepicker($.extend(o, custom));
		}).on("click", ".btnGoogleMapsApi", function (e) {
			var $this = $(this);
			$.post("index.php?controller=pjAdminListings&action=pjActionGetGeocode", $(this).closest("form").serialize()).done(function (data) {
				if (data.code !== undefined && data.code == 200) {
					$("#lat").val(data.lat);
					$("#lng").val(data.lng);
					$this.siblings("span").hide().html("");
				} else {
					$this.siblings("span").html("<br>" + myLabel.address_not_found).show();
				}
			});
		}).on("click", ".pj-checkbox", function () {
			var $this = $(this);
			if ($this.find("input[type='checkbox']").is(":checked")) {
				$this.addClass("pj-checkbox-checked");
			} else {
				$this.removeClass("pj-checkbox-checked");
			}
		}).on("click", ".listing-tip", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			return false;
		});
			
		if ($frmUpdateListing.length > 0 && validate) {
			$frmUpdateListing.validate({
				rules: {
					"listing_refid": {
						required: true,
						remote: "index.php?controller=pjAdminListings&action=pjActionCheckRefId&id=" + $frmUpdateListing.find("input[name='id']").val()
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				errorClass: "err",
				wrapper: "em",
				onkeyup: false
			});
			
			tinyMCE.init({
				// General options
				mode : "textareas",
				theme : "advanced",
				editor_selector : "mceEditor",
				editor_deselector : "mceNoEditor",
				plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
				convert_urls : false,

				// Theme options
				theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,formatselect,fontselect,fontsizeselect,justifyleft,justifycenter,justifyright,justifyfull,|,ltr,rtl",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr",
				theme_advanced_buttons4 : "cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage,insertdate,inserttime,|,forecolor,backcolor",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				width: "570"
			});
	
			$("a.fancybox").fancybox();
			
			if (spinner) {
				$("input[name='listing_bedrooms'], input[name='listing_bathrooms'], input[name='listing_adults'], input[name='listing_children']").spinner({
					min: 0
				});
				$(".field-int").spinner({
					min: 0,
					stop: function (event, ui) {
						var $this = $(this),
							name = $this.attr("name");
						if (name == "o_min_booking_lenght") {
							$("input[name='o_max_booking_lenght']").spinner("option", "min", $this.val());
						} else if (name == "o_max_booking_lenght") {
							$("input[name='o_min_booking_lenght']").spinner("option", "max", $this.val());
						}
					}
				});
				$("input[name='o_deposit_payment']").spinner("option", {
					min: 0,
					max: 100,
					step: 0.01,
					numberFormat: "n"
				});
				$("input[name='o_tax_payment']").spinner("option", {
					step: 0.01,
					numberFormat: "n"
				});
				$("input[name='o_min_booking_lenght'], input[name='o_max_booking_lenght']").spinner("option", "min", 1);
			}
			
			if (chosen) {
				$("#owner_id").chosen();
				$("#country_id").chosen();
			}
		}
		
		if (tipsy) {
			$(".listing-tip").tipsy({
				offset: 1,
				opacity: 1,
				html: true,
				gravity: "nw",
				className: "tipsy-listing"
			});
		}
		
		if ($gallery.length > 0 && gallery) {
			$gallery.gallery({
				compressUrl: "index.php?controller=pjGallery&action=pjActionCompressGallery&foreign_id=" + myGallery.foreign_id,
				getUrl: "index.php?controller=pjGallery&action=pjActionGetGallery&foreign_id=" + myGallery.foreign_id,
				deleteUrl: "index.php?controller=pjGallery&action=pjActionDeleteGallery",
				emptyUrl: "index.php?controller=pjGallery&action=pjActionEmptyGallery&foreign_id=" + myGallery.foreign_id,
				rebuildUrl: "index.php?controller=pjGallery&action=pjActionRebuildGallery&foreign_id=" + myGallery.foreign_id,
				resizeUrl: "index.php?controller=pjGallery&action=pjActionResizeGallery&id={:id}&foreign_id=" + myGallery.foreign_id,
				rotateUrl: "index.php?controller=pjGallery&action=pjActionRotateGallery",
				sortUrl: "index.php?controller=pjGallery&action=pjActionSortGallery",
				updateUrl: "index.php?controller=pjGallery&action=pjActionUpdateGallery",
				uploadUrl: "index.php?controller=pjGallery&action=pjActionUploadGallery&foreign_id=" + myGallery.foreign_id,
				watermarkUrl: "index.php?controller=pjGallery&action=pjActionWatermarkGallery&foreign_id=" + myGallery.foreign_id
			});
		}
		
		if (spinner) {
			$(".spin").spinner({
				min: 0,
				stop: function (event, ui) {
					var $this = $(this),
						$chained = $this.closest("p").find(".spin").not(this),
						name = $this.attr("name");
					if (name.match(/_from$/) !== null) {
						$chained.spinner("option", "min", $this.val());
					} else if (name.match(/_to$/) !== null) {
						$chained.spinner("option", "max", $this.val());
					}
				}
			});
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			function formatImage(val, obj) {
				var src = val ? val : 'app/web/img/backend/no_img.png';
				return ['<a href="index.php?controller=pjAdminListings&action=pjActionUpdate&id=', obj.id ,'"><img src="', src, '" style="width: 100px" /></a>'].join("");
			}
			
			function formatOwner(val, obj) {
				return ['<a href="index.php?controller=pjAdminUsers&action=pjActionUpdate&id=', obj.owner_id, '">', $.datagrid.wordwrap(obj.owner_name, 20, '<br>', true), '</a>'].join("");
			}
			
			function formatRefid(val, obj) {
				return $.datagrid.wordwrap(val, 25, '<br>', true);
			}
			
			var gridOpts = {
				buttons: [{type: "edit", url: "index.php?controller=pjAdminListings&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminListings&action=pjActionDeleteListing&id={:id}"},
				          {type: "menu", url: "#", text: myLabel.more, items:[
				              {text: myLabel.exp_date_plus_30, url: "index.php?controller=pjAdminListings&action=pjActionExpireListing&id={:id}", ajax: true, render: true},
				              {text: myLabel.view_reservations, url: "index.php?controller=pjAdminReservations&action=pjActionIndex&listing_id={:id}"}
				           ]}],
				columns: [{text: myLabel.image, type: "text", sortable: false, editable: false, renderer: formatImage},
				          {text: myLabel.ref_id, type: "text", sortable: true, editable: true, renderer: formatRefid},
				          {text: myLabel.owner, type: "text", sortable: true, editable: false, renderer: formatOwner},
				          {text: myLabel.expire, type: "date", sortable: true, editable: true,
								jqDateFormat: pjGrid.jqDateFormat,
								editableWidth: 80, 
								renderer: $.datagrid._formatDate, 
								editableRenderer: $.datagrid._formatDate,
								dateFormat: pjGrid.jsDateFormat
				          },
				          {text: myLabel.publish, type: "select", sortable: true, editable: true, editableWidth: 95, options: [
					                                                                                     {label: myLabel.active, value: "T"}, 
					                                                                                     {label: myLabel.inactive, value: "F"},
					                                                                                     {label: myLabel.exp_date, value: "E"}
					                                                                                     ], applyClass: "pj-status"}
				          ],
				dataUrl: "index.php?controller=pjAdminListings&action=pjActionGetListing" + pjGrid.queryString,
				dataType: "json",
				fields: ['image', 'listing_refid', 'owner_name', 'expire', 'status'],
				paginator: {
					actions: [
						{text: myLabel.delete_selected, url: "index.php?controller=pjAdminListings&action=pjActionDeleteListingBulk", render: true, confirmation: myLabel.delete_confirm},
						{text: myLabel.exp_date_plus_30, url: "index.php?controller=pjAdminListings&action=pjActionExpireListing", render: true, confirmation: myLabel.extend_confirm},
						{text: myLabel.published, url: "index.php?controller=pjAdminListings&action=pjActionStatusListing&status=T", render: true},
						{text: myLabel.not_published, url: "index.php?controller=pjAdminListings&action=pjActionStatusListing&status=F", render: true}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminListings&action=pjActionSaveListing&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			};
			if (pjGrid.isOwner === true) {
				function formatExtend(val, obj) {
					return ['<a class="pj-button" href="index.php?controller=pjAdminListings&action=pjActionPayment&id=', val, '">', myLabel.extend_exp_date, '</a>'].join("");
				}
				
				gridOpts.buttons = [
				    {type: "edit", url: "index.php?controller=pjAdminListings&action=pjActionUpdate&id={:id}"},
				    {type: "delete", url: "index.php?controller=pjAdminListings&action=pjActionDeleteListing&id={:id}"}
				];
				gridOpts.columns = [
				    {text: myLabel.image, type: "text", sortable: false, editable: false, renderer: formatImage, width: 100},
				    {text: myLabel.ref_id, type: "text", sortable: true, editable: true, width: 265},
					{text: myLabel.expire, type: "date", sortable: true, editable: false, renderer: $.datagrid._formatDate, dateFormat: pjGrid.jsDateFormat, width: 70},
					{text: "", type: "text", sortable: false, editable: false, renderer: formatExtend, width: 165}
				];
				gridOpts.fields = ['image', 'listing_refid', 'expire', 'id'];
				gridOpts.paginator.actions = [{text: myLabel.delete_selected, url: "index.php?controller=pjAdminListings&action=pjActionDeleteListingBulk", render: true, confirmation: myLabel.delete_confirm}];
			}
			
			var $grid = $("#grid").datagrid(gridOpts);
			
			$(document).on("click", ".btn-all", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					status: "",
					is_featured: "",
					listing_refid: "",
					country_id: "",
					type_id: "",
					user_id: "",
					adults_from: "",
					adults_to: "",
					children_from: "",
					children_to: "",
					bedrooms_from: "",
					bedrooms_to: "",
					bathrooms_from: "",
					bathrooms_to: "",
					floor_area_from: "",
					floor_area_to: ""
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminListings&action=pjActionGetListing", "id", "DESC", content.page, content.rowCount);
				return false;
			}).on("click", ".btn-filter", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache"),
					obj = {};
				$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
				obj.status = "";
				obj.is_featured = "";
				obj[$this.data("column")] = $this.data("value");
				$.extend(cache, obj);
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminListings&action=pjActionGetListing", "id", "DESC", content.page, content.rowCount);
				return false;
			}).on("click", ".pj-button-detailed, .pj-button-detailed-arrow", function (e) {
				e.stopPropagation();
				$(".pj-form-filter-advanced").toggle();
			}).on("submit", ".frm-filter-advanced", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var obj = {},
					$this = $(this),
					arr = $this.serializeArray(),
					content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
					obj[arr[i].name] = arr[i].value;
				}
				$.extend(cache, obj);
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminListings&action=pjActionGetListing", "id", "ASC", content.page, content.rowCount);
				return false;
			}).on("reset", ".frm-filter-advanced", function (e) {
				$(".pj-button-detailed").trigger("click");
				if (chosen) {
					$("#user_id").val('').trigger("liszt:updated");
				}
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
				$grid.datagrid("load", "index.php?controller=pjAdminListings&action=pjActionGetListing", "id", "ASC", content.page, content.rowCount);
				return false;
			});
			
		}
		
		$(document).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
		});
	});
})(jQuery_1_8_2);