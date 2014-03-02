/*!
 * @author Dimitar Ivanov
 */
(function ($, undefined) {
	var PROP_NAME = 'gallery',
		FALSE = false,
		TRUE = true;

	function Gallery() {
		this._defaults = {
			content: {},
			compressUrl: null,
			getUrl: null,
			deleteUrl: null,
			emptyUrl: null,
			rebuildUrl: null,
			resizeUrl: null,
			rotateUrl: null,
			sortUrl: null,
			updateUrl: null,
			uploadUrl: null,
			watermarkUrl: null
		};
		
		this.messages = {
			alt: "ALT",
			btn_delete: "Delete",
			btn_cancel: "Cancel",
			btn_save: "Save",
			btn_set_watermark: "Set watermark",
			btn_clear_current: "Clear current one",
			btn_compress: "Compress",
			btn_recreate: "Re-create thumbs",
			compress_note: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur id consectetur magna. Nulla facilisi. Sed id dolor ante.",
			compression: "Compression",
			delete_all: "Delete All",
			delete_confirmation: "Delete confirmation",
			delete_confirmation_single: "Are you sure you want to delete selected image?",
			delete_confirmation_multi: "Are you sure you want to delete all images?",
			edit: "Edit",
			empty_result: "No images uploaded yet.",
			erase: "Delete",
			image_settings: "Image settings",
			move: "Move",
			originals: "Originals",
			photos: "photos",
			position: "Position",
			resize: "Resize/Crop",
			rotate: "Rotate",
			thumbs: "Thumbs",
			upload: "Upload",
			watermark: "Watermark",
			watermark_position: "Watermark position",
			watermark_positions: {
				tl: "Top Left",
				tr: "Top Right",
				tc: "Top Center",
				bl: "Bottom Left",
				br: "Bottom Right",
				bc: "Bottom Center",
				cl: "Center Left",
				cr: "Center Right",
				cc: "Center Center"
			}
		};
	}
	
	Gallery.formatSize = function (bytes) {
		var size = parseInt(bytes, 10) / 1024;
		if (size > 1023) {
			size = (size / 1024).toFixed(1) + " MB";
		} else {
			size = Math.ceil(size) + " KB";
		}
		return size;
	};
	
	Gallery.prototype = {
		_attachGallery: function (target, settings) {
			if (this._getInst(target)) {
				return FALSE;
			}
			var buttons,
				$target = $(target),
				self = this,
				inst = self._newInst($target);
			
			$.extend(inst.settings, self._defaults, settings);

			$target.addClass("pj-gallery").on("mouseenter", ".pj-gallery-box", function (e) {
				e.stopPropagation();
				$(this).addClass("pj-gallery-box-hover");
			}).on("mouseleave", ".pj-gallery-box", function (e) {
				e.stopPropagation();
				$(this).removeClass("pj-gallery-box-hover");
			}).on("click", ".pj-gallery-move", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				return FALSE;
			}).on("click", ".pj-gallery-edit", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					$dialog = $("#pj-gallery-edit-" + inst.uid);
				if ($dialog.length > 0 && $.fn.dialog !== undefined) {
					$dialog.data("id", $this.data("id")).data("box", $this.closest(".pj-gallery-box")).dialog("open");
				}
				return FALSE;
			}).on("click", ".pj-gallery-delete", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					$dialog = $("#pj-gallery-delete-" + inst.uid);
				if ($dialog.length > 0 && $.fn.dialog !== undefined) {
					$dialog.data("id", $this.data("id")).data("box", $this.closest(".pj-gallery-box")).dialog("open");
				}
				return FALSE;
			}).on("click", ".pj-gallery-rotate", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$.post(inst.settings.rotateUrl, {
					id: $(this).data("id")
				}).done(function (data) {
					self._loadGallery.call(self, target, inst.settings.getUrl);
				});
				return FALSE;
			}).on("click", ".pj-selector-config", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var diff, lf,
					$this = $(this),
					$list = $this.siblings(".pj-menu-list-wrap");
				diff = Math.ceil( ($list.outerWidth() - $this.outerWidth()) / 2 );
				if (diff > 0) {
					lf = $this.offset().left - diff;
					if (lf < 0) {
						lf = 0;
					}
				} else {
					lf  = $this.offset().left + diff;
				}
				$list.css({
					"top": $this.offset().top + $this.outerHeight() + 2,
					"left": lf
				});
			
				$list.toggle();
				$(".pj-menu-list-wrap").not($list).hide();
				return FALSE;
			}).on("click", ".pj-selector-watermark", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					$dialog = $("#pj-gallery-watermark-" + inst.uid);
				if ($dialog.length > 0 && $.fn.dialog !== undefined) {
					$dialog.dialog("open");
					$this.closest(".pj-menu-list-wrap").hide();
				}
				return FALSE;
			}).on("click", ".pj-selector-compression", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					$dialog = $("#pj-gallery-compression-" + inst.uid);
				if ($dialog.length > 0 && $.fn.dialog !== undefined) {
					$dialog.dialog("open");
					$this.closest(".pj-menu-list-wrap").hide();
				}
				return FALSE;
			}).on("click", ".pj-selector-delete-all", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					$dialog = $("#pj-gallery-delete-all-" + inst.uid);
				if ($dialog.length > 0 && $.fn.dialog !== undefined) {
					$dialog.dialog("open");
					$this.closest(".pj-menu-list-wrap").hide();
				}
				return FALSE;
			});
			
			$(document).on("click", "*", function (e) {
				if (!$(e.target).closest(".pj-menu-list-wrap").length) {
					$(".pj-menu-list-wrap").hide();
				}
			});
			
			$("<div>").addClass("pj-preloader").insertAfter($target)
				.hide()
				.ajaxStart(function() {
					$(this).css({
						left: $target.position().left,
						top: $target.position().top,
						width: $target.width() + "px",
						height: $target.outerHeight() + "px"
					}).show();
				})
				.ajaxStop(function() {
					$(this).hide();
				});
			
			buttons = {};
			buttons[self.messages.btn_delete] = function () {
				var $this = $(this);
				$.post(inst.settings.deleteUrl, {
					"id": $this.data("id")
				}).done(function (data) {
					if (data.code === undefined) {
						return;
					}
					switch (data.code) {
					case 200:
						$this.data("box").css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
							self._loadGallery.call(self, target, inst.settings.getUrl);
							$this.dialog("close");
						});
						break;
					}
				});
			};
			buttons[self.messages.btn_cancel] = function () {
				$(this).dialog("close");
			};
			
			$("<div>", {
				"id": "pj-gallery-delete-" + inst.uid,
				"title": self.messages.delete_confirmation
			})
				.hide()
				.html(self.messages.delete_confirmation_single)
				.insertAfter($target)
				.dialog({
					modal: true,
					resizable: false,
					draggable: false,
					autoOpen: false,
					buttons: buttons
				});
			
			buttons = {};
			buttons[self.messages.btn_save] = function () {
				var $this = $(this);
				$this.dialog("close");
				$.post(inst.settings.updateUrl, $this.find(":input").serialize() + "&id=" + $this.data("id")).done(function (data) {
					self._loadGallery.call(self, target, inst.settings.getUrl);
				});
			};
			buttons[self.messages.btn_cancel] = function () {
				$(this).dialog("close");
			};

			$("<div>", {
				"id": "pj-gallery-edit-" + inst.uid,
				"title": self.messages.image_settings
			})
				.hide()
				.insertAfter($target)
				.dialog({
					modal: true,
					resizable: false,
					draggable: false,
					autoOpen: false,
					width: 500,
					open: function () {
						var $this = $(this),
							id = $this.data("id");
						$this.html("");						
						$.get(inst.settings.updateUrl, {
							"id": id
						}).done(function (data) {
							var $p = $("<p>");
							$("<label>").addClass("title").text(self.messages.alt).appendTo($p);
							$("<input>", {
								"type": "text",
								"name": "alt",
								"value": data.alt
							}).addClass("pj-form-field w300").appendTo($p);
							$p.appendTo($this);
							
							$p = $("<p>");
							$("<label>").addClass("title").text(self.messages.watermark).appendTo($p);
							$("<input>", {
								"type": "text",
								"name": "watermark",
								"value": data.watermark
							}).addClass("pj-form-field w300").appendTo($p);
							$p.appendTo($this);
							
							$p = $("<p>");
							$("<label>").addClass("title").text(self.messages.watermark_position).appendTo($p);
							$s = $("<select>", {
								"name": "position"
							}).addClass("pj-form-field");
							
							for (var x in self.messages.watermark_positions) {
								if (self.messages.watermark_positions.hasOwnProperty(x)) {
									$("<option>", {
										"value": x
									}).text(self.messages.watermark_positions[x]).appendTo($s);
								}
							}
							$s.appendTo($p);
							$p.appendTo($this);
							
							$p = $("<div>").addClass("p5 overflow");
							$("<label>").addClass("title").text(self.messages.compression).appendTo($p);
							var $d = $("<div>", {
								"css": {
									"float": "left",
									"width": "310px"
								}
							});
							attachSliders($d);
							$d.appendTo($p);
							$p.appendTo($this);
							
							$this.addClass("form pj-form");
							$this.dialog("option", "position", "center");
						});
					},
					buttons: buttons
				});
			// Config menu -------------
			buttons = {};
			buttons[self.messages.btn_delete] = function () {
				var $this = $(this);
				$.post(inst.settings.emptyUrl).done(function (data) {
					if (data.code === undefined) {
						return;
					}
					switch (data.code) {
					case 200:
						self._loadGallery.call(self, target, inst.settings.getUrl);
						break;
					}
					$this.dialog("close");
				});
			};
			buttons[self.messages.btn_cancel] = function () {
				$(this).dialog("close");
			};
			$("<div>", {
				"id": "pj-gallery-delete-all-" + inst.uid,
				"title": self.messages.delete_confirmation
			})
				.hide()
				.html(self.messages.delete_confirmation_multi)
				.insertAfter($target)
				.dialog({
					modal: true,
					resizable: false,
					draggable: false,
					autoOpen: false,
					buttons: buttons
				});
			
			buttons = {};
			buttons[self.messages.btn_set_watermark] = function () {
				var $this = $(this);
				handleWattermarkBtn('disable');
				
				$.post(inst.settings.watermarkUrl, $this.find(":input").serialize()).done(function (data) {
					$this.dialog("close");
					handleWattermarkBtn('enable');
					self._loadGallery.call(self, target, inst.settings.getUrl);
				});
			};
			buttons[self.messages.btn_clear_current] = function () {
				var $this = $(this);
				handleWattermarkBtn('disable');
				
				$.post(inst.settings.watermarkUrl).done(function (data) {
					$this.dialog("close");
					handleWattermarkBtn('enable');
					self._loadGallery.call(self, target, inst.settings.getUrl);
				});
			};
			buttons[self.messages.btn_cancel] = function () {
				$(this).dialog("close");
			};
			
			$("<div>", {
				"id": "pj-gallery-watermark-" + inst.uid,
				"title": self.messages.watermark
			})
				.hide()
				.insertAfter($target)
				.dialog({
					modal: true,
					resizable: false,
					draggable: false,
					autoOpen: false,
					width: 500,
					open: function () {
						var $s,
							$this = $(this),
							$p = $("<p>");
						$this.html("");	
						$("<label>").addClass("title").text(self.messages.watermark).appendTo($p);
						$("<input>", {
							"type": "text",
							"name": "watermark"
						}).addClass("pj-form-field w300").appendTo($p);
						$p.appendTo($this);
						
						$p = $("<p>");
						$("<label>").addClass("title").text(self.messages.position).appendTo($p);
						$s = $("<select>", {
							"name": "position"
						}).addClass("pj-form-field");
						
						for (var x in self.messages.watermark_positions) {
							if (self.messages.watermark_positions.hasOwnProperty(x)) {
								$("<option>", {
									"value": x
								}).text(self.messages.watermark_positions[x]).appendTo($s);
							}
						}
						$s.appendTo($p);
						$p.appendTo($this);
						
						$this.addClass("form pj-form");
						$this.dialog("option", "position", "center");
					},
					buttons: buttons
				});
			
			function handleWattermarkBtn(action) {
				if (action == 'disable') {
					$(":button:contains('" + self.messages.btn_set_watermark + "')").prop("disabled", true).addClass("ui-state-disabled");
					$(":button:contains('" + self.messages.btn_clear_current + "')").prop("disabled", true).addClass("ui-state-disabled");
					$(":button:contains('" + self.messages.btn_cancel + "')").prop("disabled", true).addClass("ui-state-disabled");
				} else if (action == 'enable') {
					$(":button:contains('" + self.messages.btn_set_watermark + "')").prop("disabled", false).removeClass("ui-state-disabled");
					$(":button:contains('" + self.messages.btn_clear_current + "')").prop("disabled", false).removeClass("ui-state-disabled");
					$(":button:contains('" + self.messages.btn_cancel + "')").prop("disabled", false).removeClass("ui-state-disabled");
				}
			}
			
			function handleCompressionBtn(action) {
				if (action == 'disable') {
					$(":button:contains('" + self.messages.btn_compress + "')").prop("disabled", true).addClass("ui-state-disabled");
					$(":button:contains('" + self.messages.btn_recreate + "')").prop("disabled", true).addClass("ui-state-disabled");
					$(":button:contains('" + self.messages.btn_cancel + "')").prop("disabled", true).addClass("ui-state-disabled");
				} else if (action == 'enable') {
					$(":button:contains('" + self.messages.btn_compress + "')").prop("disabled", false).removeClass("ui-state-disabled");
					$(":button:contains('" + self.messages.btn_recreate + "')").prop("disabled", false).removeClass("ui-state-disabled");
					$(":button:contains('" + self.messages.btn_cancel + "')").prop("disabled", false).removeClass("ui-state-disabled");
				}
			}
			
			function attachSliders($this) {
				var $p = $("<p>");
				$("<label>").addClass("title").html(self.messages.originals + ' <span id="pj-gallery-orig-level-'+inst.uid+'">100</span>%').appendTo($p);
				$("<input>", {"type": "hidden", "name": "source_path_compression", "value": 100}).appendTo($p);
				$p.addClass("b5").appendTo($this);
				
				$("<div>").slider({
					min: 0,
		            max: 100,
		            value: 100,
		            slide: function(event, ui) {
		               $("#pj-gallery-orig-level-"+inst.uid).text(ui.value).parent().siblings("input[name='source_path_compression']").val(ui.value);
		            }
				}).appendTo($this);
				
				$p = $("<p>");
				$("<label>").addClass("title").html(self.messages.thumbs + ' <span id="pj-gallery-thumb-level-'+inst.uid+'">80</span>%').appendTo($p);
				$("<input>", {"type": "hidden", "name": "small_path_compression", "value": 80}).appendTo($p);
				$p.addClass("t10 b5").appendTo($this);
				
				$("<div>").slider({
					min: 0,
		            max: 100,
		            value: 80,
		            slide: function(event, ui) {
		               $("#pj-gallery-thumb-level-"+inst.uid).text(ui.value).parent().siblings("input[name='small_path_compression']").val(ui.value);
		            }
				}).appendTo($this);
			}
			
			buttons = {};
			buttons[self.messages.btn_compress] = function () {
				var $this = $(this);
				handleCompressionBtn('disable');
				$.post(inst.settings.compressUrl, $this.find(":input").serialize()).done(function (data) {
					$this.dialog("close");
					handleCompressionBtn('enable');
					self._loadGallery.call(self, target, inst.settings.getUrl);
				});
			};
			buttons[self.messages.btn_recreate] = function () {
				var $this = $(this);
				handleCompressionBtn('disable');
				$.post(inst.settings.rebuildUrl).done(function (data) {
					$this.dialog("close");
					handleCompressionBtn('enable');
					self._loadGallery.call(self, target, inst.settings.getUrl);
				});
			};
			buttons[self.messages.btn_cancel] = function () {
				$(this).dialog("close");
			};
			
			$("<div>", {
				"id": "pj-gallery-compression-" + inst.uid,
				"title": self.messages.compression
			})
				.hide()
				.insertAfter($target)
				.dialog({
					modal: true,
					resizable: false,
					draggable: false,
					autoOpen: false,
					width: 380,
					open: function () {
						var $this = $(this),
							$p = $("<p>");
						$this.html("");
							
						$p.css({
							"marginBottom": "15px"
						}).html(self.messages.compress_note).appendTo($this);
						
						attachSliders($this);
					},
					buttons: buttons
				});
			// Config menu -------------
			
			$.data(target, PROP_NAME, inst);
			
			self._loadGallery.call(self, target, inst.settings.getUrl);
		},
		_loadGallery: function (target, url) {
			var inst = this._getInst(target);
			if (!inst) {
				return FALSE;
			}
			var self = this;
			$.get(url).done(function (data) {
				inst.settings.content = data;
				$.data(target, PROP_NAME, inst);
				self._renderGallery.call(self, target);
			});
			$.data(target, PROP_NAME, inst);
		},
		_renderGallery: function (target) {
			var inst = this._getInst(target);
			if (!inst) {
				return FALSE;
			}
			var i, iCnt,
				self = this,
				rand = Math.ceil(Math.random() * 999999),
				$iHeader, $iHeaderRight, $iBox, $iTitle, $iPic, $iControl, $iA, o_size, t_size,
				$iSpan, $iList, $iItem,
				$target = $(target);
			
			$target.html("");
			// Header ----------------------------------------
			$iHeader = $("<div>").addClass("pj-gallery-header");
			
			$("<input>", {
				"type": "button",
				"value": self.messages.upload,
				"id": "pj-gallery-upload-" + inst.uid
			}).addClass("pj-button pj-gallery-upload").appendTo($iHeader);
			
			$("<div>").addClass("pj-gallery-progress").hide().appendTo($iHeader);
			
			$iHeaderRight = $("<div>").addClass("pj-gallery-header-right");
			o_size = Gallery.formatSize(inst.settings.content.originals_size).split(" ");
			t_size = Gallery.formatSize(inst.settings.content.thumbs_size).split(" ");
			$("<span>").addClass("pj-gallery-buttonset pj-gallery-buttonset-first").html("<abbr>" + inst.settings.content.total + "</abbr> " + self.messages.photos).appendTo($iHeaderRight);
			$("<span>").addClass("pj-gallery-buttonset pj-gallery-original").html(self.messages.originals + " <abbr>" + o_size[0] + "</abbr> " + o_size[1]).appendTo($iHeaderRight);
			$("<span>").addClass("pj-gallery-buttonset pj-gallery-buttonset-last").html(self.messages.thumbs + " <abbr>" + t_size[0] + "</abbr> " + t_size[1]).appendTo($iHeaderRight);
			$("<button>", {"type": "button"}).addClass("pj-button pj-button-icon pj-selector-config")
				.append('<span class="pj-button-config" />')
				.append('<span class="pj-button-down" />')
				.appendTo($iHeaderRight);
			
			$iList = $("<ul>").addClass("pj-menu-list");
			$("<li>").append('<a href="#" class="pj-selector-watermark">' + self.messages.watermark + '</a>').appendTo($iList);
			$("<li>").append('<a href="#" class="pj-selector-compression">' + self.messages.compression + '</a>').appendTo($iList);
			$("<li>").append('<a href="#" class="pj-selector-delete-all">' + self.messages.delete_all + '</a>').appendTo($iList);
			$iSpan = $("<span>").addClass("pj-menu-list-wrap").hide().append('<span class="pj-menu-list-arrow" />');
			$iList.appendTo($iSpan);
			$iSpan.appendTo($iHeaderRight);
			
			$iHeaderRight.appendTo($iHeader);
			$iHeader.appendTo($target);
			
			new AjaxUpload('pj-gallery-upload-' + inst.uid, {
				// Location of the server-side upload script
				// NOTE: You are not allowed to upload files to another domain
				action: inst.settings.uploadUrl,
				// File upload name
				name: 'image',
				// Additional data to send
				data: {},
				// Submit file after selection
				autoSubmit: true,
				// The type of data that you're expecting back from the server.
				// HTML (text) and XML are detected automatically.
				// Useful when you are using JSON data as a response, set to "json" in that case.
				// Also set server response type to text/html, otherwise it will not work in IE6
				responseType: 'json',
				// Fired after the file is selected
				// Useful when autoSubmit is disabled
				// You can return false to cancel upload
				// @param file basename of uploaded file
				// @param extension of that file
				onChange: function(file, extension){},
				// Fired before the file is uploaded
				// You can return false to cancel upload
				// @param file basename of uploaded file
				// @param extension of that file
				onSubmit: function(file, extension) {
					$target.find(".pj-gallery-progress").show();
				},
				// Fired when file upload is completed
				// WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
				// @param file basename of uploaded file
				// @param response server response
				onComplete: function(file, response) {
					$target.find(".pj-gallery-progress").hide();
					var qs = '';
					if (response.error !== undefined && response.error.length > 0) {
						qs = inst.settings.getUrl.indexOf("?") !== -1 ? "&error=" + response.error : "?error=" + response.error;
					}
					self._loadGallery.call(self, target, inst.settings.getUrl + qs);
				}
			});
			
			// Header ----------------------------------------
			if (inst.settings.content.error !== undefined && inst.settings.content.error !== null) {
				$("<p>").text(inst.settings.content.error).appendTo(target);
			}
			if (inst.settings.content.data.length === 0) {
				$("<p>").text(self.messages.empty_result).appendTo(target);
			}
			for (i = 0, iCnt = inst.settings.content.data.length; i < iCnt; i++) {
				$iBox = $("<div>", {"data-id": "id_" + inst.settings.content.data[i].id}).addClass("pj-gallery-box");
				$iTitle = $("<div>").addClass("pj-gallery-title").text(
						[inst.settings.content.data[i].source_width,
						 "x", 
						 inst.settings.content.data[i].source_height,
						 ", ",
						 Gallery.formatSize( inst.settings.content.data[i].source_size)
						 ].join(""));
				$iPic = $("<div>").addClass("pj-gallery-pic");
				$iControl = $("<div>").addClass("pj-gallery-control");
				$iA = $("<a>", {
					"href": [inst.settings.content.data[i].large_path, "?", rand].join(""),
					"target": "_blank"
				});
				$("<img>", {
					"src": [inst.settings.content.data[i].medium_path, "?", rand].join("")
				}).appendTo($iA);
				
				$("<a>", {"href": "#", "title": self.messages.move, "data-id": inst.settings.content.data[i].id}).addClass("pj-gallery-icon pj-gallery-move").append("<span />").appendTo($iControl);
				$("<a>", {"href": "#", "title": self.messages.edit, "data-id": inst.settings.content.data[i].id}).addClass("pj-gallery-icon pj-gallery-edit").append("<span />").appendTo($iControl);
				$("<a>", {"href": "#", "title": self.messages.erase, "data-id": inst.settings.content.data[i].id}).addClass("pj-gallery-icon pj-gallery-delete").append("<span />").appendTo($iControl);
				$("<a>", {
					"href": inst.settings.resizeUrl.replace(/\{:(\w+)\}/, function () {
						return inst.settings.content.data[i][arguments[1]];
					}),
					"title": self.messages.resize, "data-id": inst.settings.content.data[i].id
				}).addClass("pj-gallery-icon pj-gallery-resize").append("<span />").appendTo($iControl);
				$("<a>", {"href": "#", "title": self.messages.rotate, "data-id": inst.settings.content.data[i].id}).addClass("pj-gallery-icon pj-gallery-rotate").append("<span />").appendTo($iControl);
				
				$iA.appendTo($iPic);
				$iBox.append($iTitle).append($iPic).append($iControl);
				$target.append($iBox);
			}
			this.bindSortable.call(this, target);
			$.data(target, PROP_NAME, inst);
		},
		bindSortable: function (target) {
			if ($.fn.sortable === undefined) {
				throw new Error("jQuery Sortable widget not found");
			}
			var inst = this._getInst(target);
			if (!inst) {
				return FALSE;
			}
			var $target = $(target),
				self = this;
			
			$target.sortable({
				cursor: "move",
				placeholder: "pj-gallery-highlight",
				handle: ".pj-gallery-move",
				update: function (event, ui) {
					var sorted = $(this).sortable("serialize", {
						key: "sort[]", 
						attribute: "data-id"
					});
					$.post(inst.settings.sortUrl, sorted).done(function (data) {
						self._loadGallery.call(self, target, inst.settings.getUrl);
					});
				}
	        }).disableSelection();
		},
		_optionGallery: function (target, optName, optValue) {
			var inst = this._getInst(target);
			if (!inst) {
				return FALSE;
			}
			if (typeof optName === 'string') {
				if (arguments.length === 2) {
					return inst.settings[optName];
				} else if (arguments.length === 3) {
					inst.settings[optName] = optValue;
				}
			} else if (typeof optName === 'object') {
				$.extend(inst.settings, optName);
			}
			$.data(target, PROP_NAME, inst);
		},
		_newInst: function(target) {
			var id = target[0].id.replace(/([^A-Za-z0-9_-])/g, '\\\\$1');
			return {
				id: id, 
				input: target, 
				uid: Math.floor(Math.random() * 99999999),
				settings: {}
			}; 
		},
		_getInst: function(target) {
			try {
				return $.data(target, PROP_NAME);
			}
			catch (err) {
				throw 'Missing instance data for this gallery';
			}
		}
	};

	$.fn.gallery = function (options) {
		
		var otherArgs = Array.prototype.slice.call(arguments, 1);
		if (typeof options == 'string' && options == 'isDisabled') {
			return $.gallery['_' + options + 'Gallery'].apply($.gallery, [this[0]].concat(otherArgs));
		}
		
		if (options == 'option' && arguments.length == 2 && typeof arguments[1] == 'string') {
			return $.gallery['_' + options + 'Gallery'].apply($.gallery, [this[0]].concat(otherArgs));
		}
		
		return this.each(function() {
			typeof options == 'string' ?
				$.gallery['_' + options + 'Gallery'].apply($.gallery, [this].concat(otherArgs)) :
				$.gallery._attachGallery(this, options);
		});
	};
	
	$.gallery = new Gallery(); // singleton instance
	$.gallery.version = "0.1";
})(jQuery);