var VRL = VRL || {};
VRL.Utils = {
	bindTooltips: function () {
		if (typeof JABB == 'undefined') return;
		var c = [], t = [], pvc = document.getElementById('property-view-calendars');
		c[0] = JABB.Utils.getElementsByClass('calendar', pvc, 'TD');
		c[1] = JABB.Utils.getElementsByClass('calendarToday', pvc, 'TD');
		c[2] = JABB.Utils.getElementsByClass('calendarReserved', pvc, 'TD');
		c[3] = JABB.Utils.getElementsByClass('calendarReservedLeft', pvc, 'TD');
		c[4] = JABB.Utils.getElementsByClass('calendarReservedRight', pvc, 'TD');
		t = c[0].concat(c[1], c[2], c[3], c[4]);
		for (var i = 0, len = t.length; i < len; i++) {
			JABB.Utils.addEvent(t[i], "mouseover", function () {
				var tmp = document.getElementById('t_' + this.getAttribute("id"));
				if (tmp) {
					tmp.style.visibility = 'visible';
				}
			});
			JABB.Utils.addEvent(t[i], "mouseout", function () {
				var tmp = document.getElementById('t_' + this.getAttribute("id"));
				if (tmp) {
					tmp.style.visibility = 'hidden';
				}
			});
		}
	},
	calendarCallback: function (r) {
		var pvc = document.getElementById('property-view-calendars');
		if (pvc) {
			pvc.innerHTML = r.responseText;
		}
		VRL.Utils.bindTooltips();
	},
	calendarDate: function (get, date, num) {
		var d = date.split("-");
		var n = new Date(d[0], d[1] - 1, d[2]);
		switch (get) {
			case 'prev':
				var newDate = new Date(new Date(n).setMonth(n.getMonth() - num));
			break;
			case 'next':
			default:
				var newDate = new Date(new Date(n).setMonth(n.getMonth() + num));
			break;
		}		
		return newDate.getFullYear() + "-" + (newDate.getMonth() + 1) + "-" + newDate.getDate();
	},
	changeDate: function () {
		var str = JABB.Utils.serialize(this.form);
		JABB.Ajax.sendRequest(this.form.folder.value + "index.php?controller=pjListings&action=pjActionGetPrice", function (xhr) {
			var el = JABB.Utils.getElementsByClass('vrPriceBox', null, "DIV")[0];
			if (el) {
				el.innerHTML = xhr.responseText;
			}
		}, str);
	},
	changePM: function (el) {
		var cc = JABB.Utils.getElementsByClass('vrCC', el.form, 'p'),
			bank = JABB.Utils.getElementsByClass('vrBank', el.form, 'p')[0];
		
		for (var i = 0, len = cc.length; i < len; i++) {
			cc[i].style.display = 'none';
		}
		
		if (bank) {
			bank.style.display = 'none';
		}
		
		switch (el.options[el.selectedIndex].value) {
			case 'creditcard':
				for (var i = 0, len = cc.length; i < len; i++) {
					cc[i].style.display = 'block';
				}
				break;
			case 'bank':
				if (bank) {
					bank.style.display = 'block';
				}
				break;
		}
	},
	submitRequest : function (event, formName, containerId) {
		var postData = [],
			re = /([0-9a-zA-Z\.\-\_]+)@([0-9a-zA-Z\.\-\_]+)\.([0-9a-zA-Z\.\-\_]+)/,
			msg = '',
			name = document.forms[formName].name,
			email = document.forms[formName].email,
			verification = document.forms[formName].verification,
			pm = document.forms[formName].payment_method;
			
		if (name && name.value == '') {
			msg += '\n' + VRL.Msg.bf_name;
		}
		
		if (email) {
			if (email.value == '') {
				msg += '\n' + VRL.Msg.bf_email;
			}
			if (email.value != '' && email.value.match(re) == null) {
				msg += '\n' + VRL.Msg.bf_email_inv;
			}
		}
		
		if (pm) {
			switch (pm.options[pm.selectedIndex].value) {
				case '':
					msg += '\n' + VRL.Msg.bf_pm;
					break;
				case 'creditcard':
					var cc_type = document.forms[formName].cc_type, 
						cc_num = document.forms[formName].cc_num,
						cc_code = document.forms[formName].cc_code,
						cc_exp = document.forms[formName].cc_exp;
					if (!cc_type) {
						msg += '\n' + VRL.Msg.bf_cc_type_mis;
					} else if (cc_type.options[cc_type.selectedIndex].value == '') {
						msg += '\n' + VRL.Msg.bf_cc_type;
					}
					if (!cc_num) {
						msg += '\n' + VRL.Msg.bf_cc_num_mis;
					} else if (cc_num.value == '' || cc_num.value.match(/\D+/g) !== null) {
						msg += '\n' + VRL.Msg.bf_cc_num;
					}
					if (!cc_code) {
						msg += '\n' + VRL.Msg.bf_cc_code_mis;
					} else if (cc_code.value == '' || cc_code.value.match(/\D+/g) !== null) {
						msg += '\n' + VRL.Msg.bf_cc_code;
					}
					if (!cc_exp) {
						msg += '\n' + VRL.Msg.bf_cc_exp_mis;
					} else if (cc_exp.value == '' || cc_exp.value.length != 7 || !/(0[1-9]|1[0-2])\/[0-9]{2}/.test(cc_exp.value)) {
						msg += '\n' + VRL.Msg.bf_cc_exp;
					}
					break;
			}
		}
		
		if (verification && verification.value == '') {
			msg += '\n' + VRL.Msg.bf_verify;
		}
		
		if (msg != '') {
			alert(VRL.Msg.bf_note + msg);
		} else {
			postData = JABB.Utils.serialize(document.forms[formName]);			
			var folder = document.forms[formName].folder.value;
	
			JABB.Ajax.postJSON(document.forms[formName].booking_url.value, function (data) {
				if (!data.code) return;
				switch (data.code) {
					case 11:
						JABB.Ajax.sendRequest(folder + 'index.php?controller=pjListings&action=pjActionGetPaymentForm&booking_id=' + data.booking_id, function (result) {
							var c = JABB.Utils.getElementsByClass("vrl-booking-form", null, "DIV")[0];//document.getElementById(containerId);
							if (c) {
								c.innerHTML = result.responseText;
								var p = document.forms['vrPaypal'],
									a = document.forms['vrAuthorize'];
								if (p) {
									p.submit();
								} else if (a) {
									a.submit();
								}
							}
						}, postData);						
						break;
					default:
						JABB.Ajax.sendRequest(folder + 'index.php?controller=pjListings&action=pjActionGetRequest&status=' + data.code, function (result) {
							var c = document.getElementById(containerId);
							if (c) {
								//c.innerHTML = result.responseText;
								var s = result.responseText.split("--LIMITER--");
								var c0 = document.getElementById("property-view-calendars"),
									c1 = document.getElementById("property-view-booking-form");
								if (c0) {
									c0.innerHTML = s[0];
								}
								if (c1) {
									c1.innerHTML = s[1];
								}
								myVR.bindProperty();
							}
						}, postData);
						break;
				}
			}, postData);
		}
		
		if (event.preventDefault) {
			event.preventDefault();
		}
	},
	resetLoginForm: function(formName) {
		document.forms[formName].login_email.value = '';
		document.forms[formName].login_password.value = '';
	},
	resetRegistrationForm: function(formName) {
		document.forms[formName].register_email.value = '';
		document.forms[formName].register_password.value = '';
		document.forms[formName].name.value = '';
	},
	submitLoginForm : function (formName) {
		var postData = [],
			re = /([0-9a-zA-Z\.\-\_]+)@([0-9a-zA-Z\.\-\_]+)\.([0-9a-zA-Z\.\-\_]+)/,
			msg = '',
			email = document.forms[formName].login_email.value,
			password = document.forms[formName].login_password.value;
					
		if (email == '') {
			msg += '\n' + VRL.Msg.log_email;
		}
		
		if (email != '' && email.match(re) == null) {
			msg += '\n' + VRL.Msg.log_email_inv;
		}
		
		if (password == '') {
			msg += '\n' + VRL.Msg.log_pass;
		}
		
		if (msg != '') {
			alert(VRL.Msg.log_note + msg);
			return false;
		} else {
			return true;
		}
	},
	submitRegistrationForm : function (formName) {
		var postData = [],
			re = /([0-9a-zA-Z\.\-\_]+)@([0-9a-zA-Z\.\-\_]+)\.([0-9a-zA-Z\.\-\_]+)/,
			msg = '',
			email = document.forms[formName].register_email,
			password = document.forms[formName].register_password,
			re_password = document.forms[formName].register_password_repeat,
			name = document.forms[formName].name,
			verification = document.forms[formName].verification;
					
		if (!email || email.value == '') {
			msg += '\n' + VRL.Msg.reg_email;
		}
		
		if (email.length > 0 && email.value != '' && email.value.match(re) == null) {
			msg += '\n' + VRL.Msg.reg_email_inv;
		}
		
		if (!password || password.value == '') {
			msg += '\n' + VRL.Msg.reg_pass;
		}
		
		if (!re_password || re_password.value == '') {
			msg += '\n' + VRL.Msg.reg_repeat;
		}
		
		if (password.length > 0 && re_password.length > 0 && password.value != '' && re_password.value != '' && password.value != re_password.value) {
			msg += '\n' + VRL.Msg.reg_pass_diff;
		}
		
		if (!name || name.value == '') {
			msg += '\n' + VRL.Msg.reg_name;
		}
		
		if (!verification || verification.value == '') {
			msg += '\n' + VRL.Msg.reg_verification;
		}
		
		if (msg != '') {
			alert(VRL.Msg.reg_note + msg);
			return false;
		} else {
			return true;
		}
	},
	changeCountry: function (el, folder, stateClass) {
		var url = [folder, "index.php?controller=pjListings&action=pjActionGetStates&country_id=", el.options[el.selectedIndex].value, "&stateClass=", stateClass].join("");
		JABB.Ajax.sendRequest(url, function (xhr) {
			var c = document.getElementById("vrlStateBox");
			if (c) {
				c.innerHTML = xhr.responseText; 
			}
		});
	}
};
VRL.Utils.bindTooltips();

(function (window, undefined) {
	function VR(options) {
		if (!(this instanceof VR)) {
			return new VR(options);
		}
		this.opts = {};
		this.init.call(this, options);
		return this;
	}
	
	function Carousel(options) {
		if (!(this instanceof Carousel)) {
			return new Carousel(options);
		}
		this.opts = {};
		this.init.call(this, options);
		return this;
	}
	function stripPx(str) {
		return parseInt(str.replace("px", ""), 10);
	}
	Carousel.prototype = {
		prev: function () {
			var lft = stripPx(this.slideHandle.style.left);
			if (lft + this.opts.step <= 0) {
				this.slideHandle.style.left = (lft + this.opts.step) + "px";
			}
			return this;
		},
		next: function () {
			var lft = stripPx(this.slideHandle.style.left);
			if (lft - this.opts.step > -this.opts.step * this.itemHandle.length) {
				this.slideHandle.style.left = (lft - this.opts.step) + "px";
			}
			return this;
		},
		init: function (options) {
			var self = this;
			self.opts = options;

			self.handle = JABB.Utils.getElementsByClass(self.opts.handle)[0];
			self.itemHandle = JABB.Utils.getElementsByClass(self.opts.itemHandle, self.handle, "DIV");
			self.leftHandle = JABB.Utils.getElementsByClass(self.opts.leftHandle, null, "A")[0];
			self.rightHandle = JABB.Utils.getElementsByClass(self.opts.rightHandle, null, "A")[0];
			self.slideHandle = JABB.Utils.getElementsByClass(self.opts.slideHandle, self.handle, "DIV")[0];

			if (self.leftHandle) {
				self.leftHandle.onclick = function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					self.prev.call(self);
					return false;
				};
			}
			if (self.rightHandle) {
				self.rightHandle.onclick = function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					self.next.call(self);
					return false;
				};
			}
			if (self.slideHandle) {
				self.slideHandle.style.width = (self.opts.step * self.itemHandle.length) + "px";
			}
			
			return self;
		}
	};
	
	VR.prototype = {
		bindList: function () {
			var prop = JABB.Utils.getElementsByClass("property-listing");
			for (var i = 0, iCnt = prop.length; i < iCnt; i++) {
				prop[i].onmouseover = function (e) {
					JABB.Utils.addClass(this, "property-listing-hover");
				};
				prop[i].onmouseout = function (e) {
					JABB.Utils.removeClass(this, "property-listing-hover");
				};
			}
		},
		bindGrid: function () {
			var prop = JABB.Utils.getElementsByClass("property-grid", null, "DIV");
			for (var i = 0, iCnt = prop.length; i < iCnt; i++) {
				prop[i].onmouseover = function (e) {
					var offset = JABB.Utils.getOffset(this),
						top = offset.top,
						left = offset.left;
					
					var w = JABB.Utils.getElementsByClass("property-grid-wrap", null, "DIV")[0];
					if (w) {
						var o = JABB.Utils.getOffset(w);
						left = left - o.left;
						top = top - o.top;
					}
					var m = JABB.Utils.getElementsByClass("property-grid-mask", null, "DIV")[0];
					if (m) {
						m.innerHTML = this.innerHTML;
						var d = JABB.Utils.getElementsByClass("property-grid-details", m, "DIV")[0];
						var b = JABB.Utils.getElementsByClass("property-grid-btn", m, "DIV")[0];
						if (d) {
							d.style.display = "block";
						}
						if (b) {
							b.style.display = "block";
						}
						m.style.top = top + "px";
						m.style.left = left + "px";
						m.style.display = "block";
					}
					e.stopPropagation();
				};
			}
			var mask = JABB.Utils.getElementsByClass("property-grid-mask", null, "DIV")[0];
			if (mask) {
				mask.onmouseout = function (e) {
					if (!e) var e = window.event;
					var tg = (window.event) ? e.srcElement : e.target;
					if (tg.className != "property-grid-mask") return;
					var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
					while (reltg != tg && reltg.nodeName != 'BODY')
						reltg= reltg.parentNode
					if (reltg== tg) return;
					
					this.style.display = "none";
					this.innerHTML = "";
				};
			}
		},
		bindProperty: function () {
			var df = document.getElementById("vrl_date_from"),
				dt = document.getElementById("vrl_date_to"),
				carousel = JABB.Utils.getElementsByClass("property-carousel")[0],
				df_api, dt_api, c_api;

			if (df) {
				df_api = new Calendar({
					element: "vrl_date_from",
					disablePast: true,
					dateFormat: this.opts.dateFormat,
					startDay: this.opts.startDay,					
					onSelect: function (element, selectedDate, date, cell) {
						VRL.Utils.changeDate.call(element);
						var newDate = new Date(date);
						dt_api.option({
							"minDate": newDate,
							"month": newDate.getMonth(),
							"year": newDate.getFullYear()
						}).refresh();

						if (dt_api.selectedDate < newDate) {
							dt_api.setDate(date);
							dt_api.element.value = selectedDate;
						}
					}
				});
				var pdf = document.getElementById("vrl_datepicker_from");
				if (pdf) {
					pdf.onclick = function () {
						df_api.open();
					};
				}
			}
			if (dt) {
				dt_api = new Calendar({
					element: "vrl_date_to",
					disablePast: true,
					dateFormat: this.opts.dateFormat,
					startDay: this.opts.startDay,
					onSelect: function (element, selectedDate, date, cell) {
						VRL.Utils.changeDate.call(element);
					}
				});
				var pdt = document.getElementById("vrl_datepicker_to");
				if (pdt) {
					pdt.onclick = function () {
						dt_api.open();
					};
				}
			}
			if (carousel) {
				c_api = new Carousel({
					step: 95,
					handle: "property-carousel",
					leftHandle: "property-carousel-nav-left",
					rightHandle: "property-carousel-nav-right",
					slideHandle: "property-carousel-slide",
					itemHandle: "property-carousel-item"
				}); 
			}
		},
		bindSearch: function () {
			var df = document.getElementById("vrl_d_from"),
				dt = document.getElementById("vrl_d_to"),
				df_api, dt_api;
	
			if (df) {
				df_api = new Calendar({
					element: "vrl_d_from",
					disablePast: true,
					dateFormat: this.opts.dateFormat,
					startDay: this.opts.startDay,
					onSelect: function (element, selectedDate, date, cell) {
						var newDate = new Date(date);
						dt_api.option({
							"minDate": newDate,
							"month": newDate.getMonth(),
							"year": newDate.getFullYear()
						}).refresh();

						if (dt_api.selectedDate < newDate) {
							dt_api.setDate(date);
							dt_api.element.value = selectedDate;
						}
					}
				});
				var pdf = document.getElementById("vrl_dpicker_from");
				if (pdf) {
					pdf.onclick = function () {
						df_api.open();
					};
				}
			}
			if (dt) {
				dt_api = new Calendar({
					element: "vrl_d_to",
					disablePast: true,
					dateFormat: this.opts.dateFormat,
					startDay: this.opts.startDay
				});
				var pdt = document.getElementById("vrl_dpicker_to");
				if (pdt) {
					pdt.onclick = function () {
						dt_api.open();
					};
				}
			}
		},
		init: function (options) {
			this.opts = options;
			this.bindList.call(this);
			this.bindGrid.call(this);
			this.bindProperty.call(this);
			this.bindSearch.call(this);
			return this;
		}
	};
	window.VR = VR;
})(window);

var myVR = new VR(VRL.Opts);