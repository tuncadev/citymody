(function ($) {
	"use strict";
	$(document).ready(function () {
		function civi_invoice_print() {
			$("#invoice-print").on("click", function (e) {
				e.preventDefault();
				var $this = $(this),
					invoice_id = $this.data("invoice-id"),
					ajax_url = $this.data("ajax-url"),
					invoice_print_window = window.open(
						"",
						"Invoice Print Window",
						"scrollbars=0,menubar=0,resizable=1,width=991 ,height=800"
					);
				$.ajax({
					type: "POST",
					url: ajax_url,
					data: {
						action: "civi_invoice_print_ajax",
						invoice_id: invoice_id,
						isRTL: $("body").hasClass("rtl") ? "true" : "false",
					},
					success: function (html) {
						invoice_print_window.document.write(html);
						invoice_print_window.document.close();
						invoice_print_window.focus();
					},
					error: function (html) {
						console.log(html);
					},
				});
			});
		}
		civi_invoice_print();
	});
})(jQuery);
