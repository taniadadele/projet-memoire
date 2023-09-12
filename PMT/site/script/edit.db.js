	var matched, browser;

	jQuery.uaMatch = function( ua ) {
		ua = ua.toLowerCase();

		var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
			/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
			/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
			/(msie) ([\w.]+)/.exec( ua ) ||
			ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
			[];

		return {
			browser: match[ 1 ] || "",
			version: match[ 2 ] || "0"
		};
	};

	matched = jQuery.uaMatch( navigator.userAgent );
	browser = {};

	if ( matched.browser ) {
		browser[ matched.browser ] = true;
		browser.version = matched.version;
	}

	// Chrome is Webkit, but Webkit is also Safari.
	if ( browser.chrome ) {
		browser.webkit = true;
	} else if ( browser.webkit ) {
		browser.safari = true;
	}

	jQuery.browser = browser;

/* The following function creates a new input field and then calls datePickerController.create();
   to dynamically create a new datePicker widgit for it */
function newline() {
		var total = document.getElementById("newline-wrapper").getElementsByTagName("table").length;
		total++;

		// Clone the first div in the series
		var tbl = document.getElementById("newline-wrapper").getElementsByTagName("table")[0].cloneNode(true);

		// DOM inject the wrapper div
		document.getElementById("newline-wrapper").appendChild(tbl);

		var buts = tbl.getElementsByTagName("a");
		if(buts.length) {
				buts[0].parentNode.removeChild(buts[0]);
				buts = null;
		}

		// Reset the cloned label's "for" attributes
		var labels = tbl.getElementsByTagName('label');

		for(var i = 0, lbl; lbl = labels[i]; i++) {
				// Set the new labels "for" attribute
				if(lbl["htmlFor"]) {
						lbl["htmlFor"] = lbl["htmlFor"].replace(/[0-9]+/g, total);
				} else if(lbl.getAttribute("for")) {
						lbl.setAttribute("for", lbl.getAttribute("for").replace(/[0-9]+/, total));
				}
		}

		// Reset the input's name and id attributes
		var inputs = tbl.getElementsByTagName('input');
		for(var i = 0, inp; inp = inputs[i]; i++) {
				// Set the new input's id and name attribute
				inp.id = inp.name = inp.id.replace(/[0-9]+/g, total);
				if(inp.type == "text") inp.value = "";
		}

		// Call the create method to create and associate a new date-picker widgit with the new input
		datePickerController.create(document.getElementById("date-" + total));

		var dp = datePickerController.datePickers["dp-normal-1"];

		// No more than 5 inputs
		if(total == 50) document.getElementById("newline").style.display = "none";

		$("#date-" + total).change(function() {
			if(!$("#date-" + (total+1)).length)
			{
				newline();
			}
		});

		// Stop the event
		return false;
}

function createNewLineButton() {
		var nlw = document.getElementById("newline-wrapper");

		var a = document.createElement("a");
		a.href="#";
		a.id = "newline";
		a.title = "Create New Input";
		a.onclick = newline;
		nlw.parentNode.appendChild(a);

		a.appendChild(document.createTextNode("Plus"));
		a.style.fontWeight = "bold";
		a.style.fontSize = "12px";
		a = null;
}

datePickerController.addEvent(window, 'load', createNewLineButton);

if (!DateAdd || typeof (DateDiff) != "function") {
	var DateAdd = function(interval, number, idate) {
		number = parseInt(number);
		var date;
		if (typeof (idate) == "string") {
			date = idate.split(/\D/);
			eval("var date = new Date(" + date.join(",") + ")");
		}
		if (typeof (idate) == "object") {
			date = new Date(idate.toString());
		}
		switch (interval) {
			case "y": date.setFullYear(date.getFullYear() + number); break;
			case "m": date.setMonth(date.getMonth() + number); break;
			case "d": date.setDate(date.getDate() + number); break;
			case "w": date.setDate(date.getDate() + 7 * number); break;
			case "h": date.setHours(date.getHours() + number); break;
			case "n": date.setMinutes(date.getMinutes() + number); break;
			case "s": date.setSeconds(date.getSeconds() + number); break;
			case "l": date.setMilliseconds(date.getMilliseconds() + number); break;
		}
		return date;
	}
}
function getHM(date)
{
	 var hour =date.getHours();
	 var minute= date.getMinutes();
	 var ret= (hour>9?hour:"0"+hour)+":"+(minute>9?minute:"0"+minute) ;
	 return ret;
}
$(document).ready(function() {
	//debugger;
	var DATA_FEED_URL = "php/datafeed.php";
	var arrT = [];
	var tt = "{0}:{1}";
	for (var i = 0; i < 24; i++) {
		arrT.push({ text: StrFormat(tt, [i >= 10 ? i : "0" + i, "00"]) }, { text: StrFormat(tt, [i >= 10 ? i : "0" + i, "30"]) });
	}
	$("#timezone").val(new Date().getTimezoneOffset()/60 * -1);
	$("#stparttime").dropdown({
		dropheight: 200,
		dropwidth:60,
		selectedchange: function() { },
		items: arrT
	});
	$("#etparttime").dropdown({
		dropheight: 200,
		dropwidth:60,
		selectedchange: function() { },
		items: arrT
	});
	var check = $("#IsAllDayEvent").click(function(e) {
		if (this.checked) {
			$("#stparttime").val("00:00").hide();
			$("#etparttime").val("00:00").hide();
		}
		else {
			var d = new Date();
			var p = 60 - d.getMinutes();
			if (p > 30) p = p - 30;
			d = DateAdd("n", p, d);
			$("#stparttime").val(getHM(d)).show();
			$("#etparttime").val(getHM(DateAdd("h", 1, d))).show();
		}
	});
	/*if (check[0].checked) {
		$("#stparttime").val("00:00").hide();
		$("#etparttime").val("00:00").hide();
	}*/
	$("#Savebtn").click(function() { $("#fmEdit").submit(); });
	$("#Closebtn").click(function() { CloseModelWindow(); });
	$("#Deletebtn").click(function() {
		 if (confirm("Are you sure to remove this event")) {
			var param = [{ "name": "calendarId", value: 8}];
			$.post(DATA_FEED_URL + "?method=remove",
				param,
				function(data){
					  if (data.IsSuccess) {
							alert(data.Msg);
							CloseModelWindow(null,true);
						}
						else {
							alert("Error occurs.\r\n" + data.Msg);
						}
				}
			,"json");
		}
	});

   $("#stpartdate,#etpartdate").datepicker({ picker: "<button class='calpick'></button>"});
	var cv =$("#colorvalue").val() ;
	if(cv=="")
	{
		cv="-1";
	}
	$("#calendarcolor").colorselect({ title: "Color", index: cv, hiddenid: "colorvalue" });
	//to define parameters of ajaxform
	var options = {
		beforeSubmit: function() {
			return true;
		},
		dataType: "json",
		success: function(data) {
			alert(data.Msg);
			if (data.IsSuccess) {
				CloseModelWindow(null,true);
			}
		}
	};
	$.validator.addMethod("date", function(value, element) {
		var arrs = value.split(i18n.datepicker.dateformat.separator);
		var year = arrs[i18n.datepicker.dateformat.year_index];
		var month = arrs[i18n.datepicker.dateformat.month_index];
		var day = arrs[i18n.datepicker.dateformat.day_index];
		var standvalue = [year,month,day].join("-");
		return this.optional(element) || /^(?:(?:1[6-9]|[2-9]\d)?\d{2}[\/\-\.](?:0?[1,3-9]|1[0-2])[\/\-\.](?:29|30))(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:1[6-9]|[2-9]\d)?\d{2}[\/\-\.](?:0?[1,3,5,7,8]|1[02])[\/\-\.]31)(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])[\/\-\.]0?2[\/\-\.]29)(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:16|[2468][048]|[3579][26])00[\/\-\.]0?2[\/\-\.]29)(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:1[6-9]|[2-9]\d)?\d{2}[\/\-\.](?:0?[1-9]|1[0-2])[\/\-\.](?:0?[1-9]|1\d|2[0-8]))(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?:\d{1,3})?)?$/.test(standvalue);
	}, "Invalid date format");
	$.validator.addMethod("time", function(value, element) {
		return this.optional(element) || /^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/.test(value);
	}, "Invalid time format");
	$.validator.addMethod("safe", function(value, element) {
		return this.optional(element) || /^[^$\<\>]+$/.test(value);
	}, "$<> not allowed");
	$("#fmEdit").validate({
		submitHandler: function(form) { $("#fmEdit").ajaxSubmit(options); },
		errorElement: "div",
		errorClass: "cusErrorPanel",
		errorPlacement: function(error, element) {
			showerror(error, element);
		}
	});
	function showerror(error, target) {
		var pos = target.position();
		var height = target.height();
		var newpos = { left: pos.left, top: pos.top + height + 2 }
		var form = $("#fmEdit");
		error.appendTo(form).css(newpos);
	}
});
