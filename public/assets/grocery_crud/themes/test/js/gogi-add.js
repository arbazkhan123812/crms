$(function () {
	$(".ptogtitle").click(function () {
		if ($(this).hasClass("vsble")) {
			$(this).removeClass("vsble");
			$("#main-table-box #crudForm").slideDown("slow");
		} else {
			$(this).addClass("vsble");
			$("#main-table-box #crudForm").slideUp("slow");
		}
	});

	var save_and_close = false;

	$("#save-and-go-back-button").click(function () {
		save_and_close = true;

		$("#crudForm").trigger("submit");
	});

	$("#crudForm").submit(function () {
		var my_crud_form = $(this);

		// Addtional
		// var textareaName = my_crud_form.find('.textckeditor5').attr('name');
		// var textareaId = my_crud_form.find('.textckeditor5').attr('id');
		// var desc = $('.ck-content').html();
        // my_crud_form.append('<input type="hidden" id="' + textareaId + '" name="' + textareaName + '" value="' + desc + '">');
		// //END..
		
		$(this).ajaxSubmit({
			url: validation_url,
			dataType: "json",
			cache: "false",
			beforeSend: function () {
				$("#FormLoading").show();
			},
			success: function (data) {
				$("#FormLoading").hide();
				if (data.success) {
					$("#crudForm").ajaxSubmit({
						dataType: "text",
						cache: "false",
						beforeSend: function () {
							$("#FormLoading").show();
						},
						success: function (result) {
							$("#FormLoading").fadeOut("slow");
							data = $.parseJSON(result);
							if (data.success) {
								var data_unique_hash = my_crud_form
									.closest(".flexigrid")
									.attr("data-unique-hash");

								$(".flexigrid[data-unique-hash=" + data_unique_hash + "]")
									.find(".ajax_refresh_and_loading")
									.trigger("click");

								if (save_and_close) {
									if (
										$("#save-and-go-back-button").closest(".ui-dialog")
											.length === 0
									) {
										window.location = data.success_list_url;
									} else {
										$(".ui-dialog-content").dialog("close");
										success_message(data.success_message);
									}

									return true;
								}

								$(".is-invalid").each(function () {
									$(this).removeClass("is-invalid");
								});
								clearForm();
								form_success_message(data.success_message);
							} else {
								alert(message_insert_error);
							}
						},
						error: function () {
							alert(message_insert_error);
							$("#FormLoading").hide();
						},
					});
				} else {
					$(".is-invalid").removeClass("is-invalid");
					// $('.invalid-feedback').css('display', 'none'); 
					$.each(data.error_fields, function (index, value) {
						// var inputField = $("#field-" + index);
						var inputField = $('input[name='+index+']');
						form_error_message(data.error_fields[index], inputField);
						inputField.addClass("is-invalid");
					});
				}
			},
			error: function () {
				error_message(message_insert_error);
				$("#FormLoading").hide();
			},
		});
		return false;
	});

	if ($("#cancel-button").closest(".ui-dialog").length === 0) {
		$("#cancel-button").click(function () {
			if (confirm(message_alert_add_form)) {
				window.location = list_url;
			}

			return false;
		});
	}
});

function clearForm() {
	$("#crudForm")
		.find(":input")
		.each(function () {
			switch (this.type) {
				case "password":
					//case 'select-multiple':
					//case 'select-one':
					//case 'text':
					//case 'textarea':
					$(this).val("");
					break;
					//case 'checkbox':
					//case 'radio':
					this.checked = false;
			}
		});

	/* Clear upload inputs  */
	$(".open-file,.gc-file-upload,.hidden-upload-input").each(function () {
		$(this).val("");
	});

	$(".upload-success-url").hide();
	$(".fileinput-button").fadeIn("normal");
	/* -------------------- */

	$(".remove-all").each(function () {
		$(this).trigger("click");
	});

	$(".chosen-multiple-select, .chosen-select, .ajax-chosen-select").each(
		function () {
			$(this).trigger("liszt:updated");
		}
	);
}
