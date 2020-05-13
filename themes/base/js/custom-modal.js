(function() {
	"use strict";

	// AJAX Show Modal
	$(document)
			.on(
					'click',
					'.showModalButton',
					function(e) {
						e.preventDefault();
						var dataId = $(this).attr("data-id");
						var MODALID = (typeof dataId == "undefined") ? "#modal"
								: "#modal_" + dataId;
						var MODALCONTENT = (typeof dataId == "undefined") ? "#modalContent"
								: "#modalContent_" + dataId;
						$(MODALID).modal('show').find(MODALCONTENT).load(
								$(this).attr('data-target'));
					});

	// Grid Ajax Delete
	$(document)
			.on(
					"click",
					".ajaxDelete",
					function(e) {
						var ok = ((typeof $(this).attr('confirm') != "undefined") ? $(
								this).attr('confirm')
								: 'Are you sure you want to delete this item?');
						if (confirm(ok)) {
							var url = $(this).attr('data-url');
							var method = (typeof $(this).attr('data-method') != "undefined") ? $(
									this).attr('data-method')
									: 'POST';

							var option = {
								url : url,
								type : method,
								success : function(response) {
									location.reload();
								}
							};
							$.ajax(option);
						}
					});

})(jQuery);
