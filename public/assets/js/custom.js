"use strict";

$(document).on('click', '.estimate_to_invoice', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: convert_estimate_to_invoice,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'estimates/estimate_to_invoice/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload()
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.invoice_to_estimate', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: convert_invoice_to_estimate,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'invoices/invoice_to_estimate/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload()
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.convert_to_client', function (e) {
	e.preventDefault();

	let save_button = $(this);
	save_button.addClass('btn-progress');

	var id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: base_url + 'leads/ajax_get_leads_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			save_button.removeClass('btn-progress');
			if (result['error'] == false) {

				$("#delete_lead").val(result['data'][0].id);
				$("#first_name").val(result['data'][0].company);
				$(".company").val(result['data'][0].company);
				$(".email").val(result['data'][0].email);
				$("#phone").val(result['data'][0].mobile);

				$("#modal-add-lead-user").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-add-lead-user").fireModal({
	title: $("#modal-add-lead-user-part").data('title'),
	body: $("#modal-add-lead-user-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-lead-user-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-payments").fireModal({
	size: 'modal-lg',
	title: $("#modal-add-payments-part").data('title'),
	body: $("#modal-add-payments-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-payments-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

function file_exists(src) {
	$.ajax({
		url: src,
		type: 'HEAD',
		error: function () {
			return 0;
		},
		success: function () {
			return 1;
		}
	});
}

$(document).on('click', '.delete_attendance', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'attendance/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload()
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '#timeline-tab', function () {
	$('#timeline_list').bootstrapTable('refresh');
});

$("#modal-add-attendance").fireModal({
	title: $("#modal-add-attendance-part").data('title'),
	body: $("#modal-add-attendance-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-attendance-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});
$(document).on('click', '.modal-edit-attendance', function (e) {
	e.preventDefault();

	let save_button = $(this);
	save_button.addClass('btn-progress');

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'attendance/get_attendance_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			save_button.removeClass('btn-progress');
			if (result['error'] == false && result['data'] != '') {

				$("#update_id").val(result['data'][0].id);

				$("#user_id").val(result['data'][0].user_id);
				$("#user_id").trigger("change");

				var time24 = false;
				if (time_format_js == 'H:mm') {
					time24 = true;
				}

				$('#check_in').daterangepicker({
					locale: { format: date_format_js + ' ' + time_format_js },
					singleDatePicker: true,
					timePicker: true,
					timePicker24Hour: time24,
					startDate: moment(new Date(result['data'][0].check_in), date_format_js + ' ' + time_format_js),
				});

				$('#check_out').daterangepicker({
					locale: { format: date_format_js + ' ' + time_format_js },
					singleDatePicker: true,
					timePicker: true,
					timePicker24Hour: time24,
					startDate: moment(new Date(result['data'][0].check_out), date_format_js + ' ' + time_format_js),
				});

				$("#note").val(result['data'][0].note);

				$("#modal-edit-attendance").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-attendance").fireModal({
	title: $("#modal-edit-attendance-part").data('title'),
	body: $("#modal-edit-attendance-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload()
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-attendance-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '.delete_support', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'support/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload()
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$("#modal-add-support").fireModal({
	title: $("#modal-add-support-part").data('title'),
	body: $("#modal-add-support-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					window.location.replace(base_url + "support/chat/" + result['data']);
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-support-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});
$(document).on('click', '.modal-edit-support', function (e) {
	e.preventDefault();

	let save_button = $(this);
	save_button.addClass('btn-progress');

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'support/get_support_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			save_button.removeClass('btn-progress');
			if (result['error'] == false && result['data'] != '') {

				$("#update_id").val(result['data'][0].id);

				$("#user_id").val(result['data'][0].user_id);
				$("#user_id").trigger("change");

				$("#status").val(result['data'][0].status);
				$("#status").trigger("change");

				$("#subject").val(result['data'][0].subject);

				$("#modal-edit-support").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-support").fireModal({
	title: $("#modal-edit-support-part").data('title'),
	body: $("#modal-edit-support-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload()
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-support-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '#agree_regi', function () {
	if ($(this).prop('checked') == true) {
		$(".savebtn").removeAttr("disabled");
	} else {
		$(".savebtn").attr("disabled", true);
	}
});

$(document).on('click', '#landing_page', function (e) {
	if ($("#landing_page_theme_div").hasClass("d-none")) {
		$("#landing_page_theme_div").removeClass("d-none");
		$("#landing_page_theme_div").addClass("d-flex");
	} else {
		$("#landing_page_theme_div").addClass("d-none");
		$("#landing_page_theme_div").removeClass("d-flex");
	}
});

$(document).on('click', '.delete_leads', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'leads/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload()
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$("#modal-add-leads").fireModal({
	title: $("#modal-add-leads-part").data('title'),
	body: $("#modal-add-leads-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-leads-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});


$(document).on('click', '.modal-edit-leads', function (e) {
	e.preventDefault();

	let save_button = $(this);
	save_button.addClass('btn-progress');

	var id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: base_url + 'leads/ajax_get_leads_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			save_button.removeClass('btn-progress');
			if (result['error'] == false) {

				$("#update_id").val(result['data'][0].id);
				$(".company").val(result['data'][0].company);
				$("#value").val(result['data'][0].value);
				$("#source").val(result['data'][0].source);
				$(".email").val(result['data'][0].email);
				$("#mobile").val(result['data'][0].mobile);
				$("#status").val(result['data'][0].status);
				$("#status").trigger("change");
				$("#assigned").val(result['data'][0].assigned);
				$("#assigned").trigger("change");

				$("#modal-edit-leads").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-leads").fireModal({
	title: $("#modal-edit-leads-part").data('title'),
	body: $("#modal-edit-leads-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-leads-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#support-form").submit(function (e) {
	e.preventDefault();
	let save_button = $(this).find('.savebtn'),
		output_status = $(this).find('.result'),
		card = $('#support-form-card');

	let card_progress = $.cardProgress(card, {
		spinner: true
	});
	save_button.addClass('btn-progress');
	output_status.html('');

	var formData = new FormData(this);
	$.ajax({
		type: 'POST',
		url: $(this).attr('action'),
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (result) {
			if (result['error'] == false) {
				location.reload();
			} else {
				output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
			}
			output_status.find('.alert').delay(4000).fadeOut();
			save_button.removeClass('btn-progress');
			card_progress.dismiss(function () {
				$('html, body').animate({
					scrollTop: output_status.offset().top
				}, 1000);
			});
		}
	});
});

$("#project-comment-form").submit(function (e) {
	e.preventDefault();
	let save_button = $(this).find('.savebtn'),
		output_status = $(this).find('.result'),
		card = $('#project-comment-card');

	let card_progress = $.cardProgress(card, {
		spinner: true
	});
	save_button.addClass('btn-progress');
	output_status.html('');

	var formData = new FormData(this);
	$.ajax({
		type: 'POST',
		url: $(this).attr('action'),
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (result) {
			if (result['error'] == false) {
				location.reload();
			} else {
				output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
			}
			output_status.find('.alert').delay(4000).fadeOut();
			save_button.removeClass('btn-progress');
			card_progress.dismiss(function () {
				$('html, body').animate({
					scrollTop: output_status.offset().top
				}, 1000);
			});
		}
	});
});

$(document).on('click', '.delete_meeting', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'meetings/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload()
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-meetings', function (e) {
	e.preventDefault();

	let save_button = $(this);
	save_button.addClass('btn-progress');

	var id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: base_url + 'meetings/ajax_get_meetings_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			save_button.removeClass('btn-progress');
			if (result['error'] == false) {

				$("#update_id").val(result['data'][0].id);

				if (result['data'][0].users != '' && result['data'][0].users != null) {
					$("#users").val(result['data'][0].users.split(','));
					$("#users").trigger("change");
				}

				var time24 = false;
				if (time_format_js == 'H:mm') {
					time24 = true;
				}

				$('#starting_date_and_time').daterangepicker({
					locale: { format: date_format_js + ' ' + time_format_js },
					singleDatePicker: true,
					timePicker: true,
					timePicker24Hour: time24,
					startDate: moment(new Date(result['data'][0].starting_date_and_time), date_format_js + ' ' + time_format_js),
				});

				$("#title").val(result['data'][0].title);

				$("#duration").val(result['data'][0].duration);

				$("#status").val(result['data'][0].status);
				$("#status").trigger("change");

				$("#modal-edit-meetings").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-meetings").fireModal({
	title: $("#modal-edit-meetings-part").data('title'),
	body: $("#modal-edit-meetings-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					$('#video_meetings_list').bootstrapTable('refresh');
					modal.modal('hide');
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-meetings-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-meetings").fireModal({
	title: $("#modal-add-meetings-part").data('title'),
	body: $("#modal-add-meetings-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-meetings-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '.delete_estimate', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'estimates/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							$('#estimates_list').bootstrapTable('refresh');
							location.reload()
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-estimates', function (e) {
	e.preventDefault();

	let save_button = $(this);
	save_button.addClass('btn-progress');

	var id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: base_url + 'estimates/ajax_get_estimates_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			save_button.removeClass('btn-progress');
			if (result['error'] == false) {

				$("#update_id").val(result['data'][0].id);

				if (result['data'][0].products_id != '' && result['data'][0].products_id != null) {
					$("#update_products_id").val(result['data'][0].products_id.split(','));
					$("#update_products_id").trigger("change");
				}

				$('#estimate_date').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: moment(result['data'][0].estimate_date, date_format_js),
				});
				$('#due_date').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: moment(result['data'][0].due_date, date_format_js),
				});

				$("#status").val(result['data'][0].status);
				$("#status").trigger("change");

				$("#to_id").val(result['data'][0].to_id);
				$("#to_id").trigger("change");

				if (result['data'][0].tax != '' && result['data'][0].tax != null) {
					$("#tax").val(result['data'][0].tax.split(','));
					$("#tax").trigger("change");
				}

				$("#note").val(result['data'][0].note);

				$("#modal-edit-estimates").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-estimates").fireModal({
	size: 'modal-lg',
	title: $("#modal-edit-estimates-part").data('title'),
	body: $("#modal-edit-estimates-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					$('#estimates_list').bootstrapTable('refresh');
					modal.modal('hide');
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-estimates-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-estimates").fireModal({
	size: 'modal-lg',
	title: $("#modal-add-estimates-part").data('title'),
	body: $("#modal-add-estimates-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-estimates-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '.delete_products', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'products/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-products', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'products/get_products_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});
			if (result['error'] == false && result['data'] != '') {
				$("#update_id").val(result['data'][0].id);
				$("#name").val(result['data'][0].name);
				$("#price").val(result['data'][0].price);

				$("#modal-edit-products").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-products").fireModal({
	title: $("#modal-edit-products-part").data('title'),
	body: $("#modal-edit-products-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-products-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-products").fireModal({
	title: $("#modal-add-products-part").data('title'),
	body: $("#modal-add-products-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-products-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

function doesFileExist(urlToFile) {
	var xhr = new XMLHttpRequest();
	xhr.open('HEAD', urlToFile, false);
	xhr.send();

	if (xhr.status == "404") {
		return false;
	} else {
		return true;
	}
}

$(document).on('click', '.delete_expenses', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'expenses/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-expenses', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'expenses/get_expenses_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});

			if (result['error'] == false) {
				$("#client_id").val(result['data'][0].client_id).trigger("change", result['data'][0].project_id);

				$("#team_member_id").val(result['data'][0].team_member_id);
				$("#team_member_id").trigger("change");
				$('#date').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: moment(result['data'][0].date, date_format_js),
				});

				$("#update_id").val(result['data'][0].id);
				$("#old_receipt").val(result['data'][0].receipt);
				$("#description").val(result['data'][0].description);
				$("#amount").val(result['data'][0].amount);

				$("#modal-edit-expenses").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-expenses").fireModal({
	title: $("#modal-edit-expenses-part").data('title'),
	body: $("#modal-edit-expenses-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-expenses-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-expenses").fireModal({
	size: 'modal-lg',
	title: $("#modal-add-expenses-part").data('title'),
	body: $("#modal-add-expenses-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-expenses-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});


// ////////////////////////////////////////////////////////////////////////////////////////////////////
$(document).on('click', '.delete_leaves', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'leaves/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});


$(document).on('click', '.modal-edit-leaves', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'leaves/get_leaves_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});

			if (result['error'] == false && result['data'] != '') {
				$("#update_id").val(result['data'][0].id);
				$("#user_id").val(result['data'][0].user_id);
				$("#user_id").trigger("change");
				$("#leave_duration").val(result['data'][0].leave_duration);

				var startingDate = moment(result['data'][0].starting_date, 'YYYY-MM-DD').format(date_format_js);
				var endingDate = moment(result['data'][0].ending_date, 'YYYY-MM-DD').format(date_format_js);
				var startingTime = moment(result['data'][0].starting_time, 'HH:mm:ss').format(time_format_js);
				var endingTime = moment(result['data'][0].ending_time, 'HH:mm:ss').format(time_format_js);

				if (result['data'][0].leave_duration.includes('Full')) {

					$('#starting_date').daterangepicker({
						locale: {
							format: date_format_js
						},
						singleDatePicker: true,
						startDate: startingDate,
					});

					$('#ending_date').daterangepicker({
						locale: {
							format: date_format_js
						},
						singleDatePicker: true,
						startDate: endingDate,
						minDate: moment(startingDate, date_format_js).startOf('day')
					});
					$('#full_day_dates_edit').show();
					$('#short_leave_dates_edit').hide();
					$('#half_day_date_edit').hide();
					$("#leave").val('Full Day Leave');
				}

				else if (result['data'][0].leave_duration.includes('Short')) {
					$('#date').daterangepicker({
						locale: {
							format: date_format_js
						},
						singleDatePicker: true,
						startDate: startingDate,
					});

					var time24 = false;
					$('#starting_time').timepicker({
						format: 'HH:mm',
						showMeridian: false,
						time24Hour: time24
					});
					$('#starting_time').timepicker('setTime', startingTime); // Set the default starting time

					$('#ending_time').timepicker({
						format: 'HH:mm',
						showMeridian: false,
						time24Hour: time24
					});
					$('#ending_time').timepicker('setTime', endingTime); // Set the default ending time

					$('#full_day_dates_edit').hide();
					$('#short_leave_dates_edit').show();
					$('#half_day_date_edit').hide();
					$("#leave").val('Short Leave');
				}

				else if (result['data'][0].leave_duration.includes('Half')) {
					$('#date_half').daterangepicker({
						locale: {
							format: date_format_js
						},
						singleDatePicker: true,
						startDate: startingDate,
					});

					if (result['data'][0].leave_duration.includes('First')) {
						var half_day_periods = "0";
					}
					else {
						var half_day_periods = "1";
					}
					$("#leave").val('Half Day Leave');
					$("#half_day_period").val(half_day_periods);
					$("#half_day_period").trigger('change');
					$('#full_day_dates_edit').hide();
					$('#short_leave_dates_edit').hide();
					$('#half_day_date_edit').show();
				}

				$("#leave_reason").val(result['data'][0].leave_reason);
				$("#leave_reason").trigger("change");
				$("#status").val(result['data'][0].status);
				$("#status").trigger("change");
				$("#type").val(result['data'][0].type);
				$("#type").trigger("change");
				$("#paid").val(result['data'][0].paid);
				$("#paid").trigger("change");
				$("#modal-edit-leaves").trigger("click");

			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});


$("#modal-edit-leaves").fireModal({
	title: $("#modal-edit-leaves-part").data('title'),
	body: $("#modal-edit-leaves-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-leaves-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});



$("#modal-add-leaves").modal({
	title: $("#modal-add-leaves-part").data('title'),
	body: $("#modal-add-leaves-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					console.log(result['as']);
					console.log(result['starting_time']);
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});
		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-leaves-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
				// Do something
			}
		}
	],
});

$(document).on('click', '.stop_timesheet_timer', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok],
		content: {
			element: 'input',
			attributes: {
				name: 'note',
				placeholder: 'Note',
				type: 'text',
			},
		},
	}).then((data) => {
		if (data) {
			$.ajax({
				type: "POST",
				url: base_url + 'projects/stop_timesheet_timer/' + id,
				data: "id=" + id + "&note=" + data,
				dataType: "json",
				success: function (result) {
					if (result['error'] == false) {
						$('#timesheet_list').bootstrapTable('refresh');
					} else {
						iziToast.error({
							title: result['message'],
							message: "",
							position: 'topRight'
						});
					}
				}
			});
		}
	});
});

// holiday/////////////////////////////////////////////////////////////////////////////////////////////////
$(document).on('click', '.delete_holiday', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'holiday/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {

						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});
$(document).on('click', '.modal-edit-holiday', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'holiday/get_holiday_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});
			if (result['error'] == false && result['data'] != '') {
				$("#update_id").val(result['data'].id);
				$("#holiday_duration").val(result['data'].leave_duration);

				var startingDate = moment(result['data'].starting_date, 'YYYY-MM-DD').format(date_format_js);
				var endingDate = moment(result['data'].ending_date, 'YYYY-MM-DD').format(date_format_js);

				$('#starting_date2').daterangepicker({
					locale: {
						format: date_format_js
					},
					singleDatePicker: true,
					startDate: startingDate,
				});

				// Create the ending date picker
				var endingDatePicker = $('#ending_date').daterangepicker({
					locale: {
						format: date_format_js
					},
					singleDatePicker: true,
					startDate: startingDate,
					minDate: moment(startingDate, date_format_js).startOf('day')
				});

				// Event listener for when the start date changes
				$('#starting_date2').on('apply.daterangepicker', function (ev, picker) {
					// Update the minDate of the ending date picker
					endingDatePicker.data('daterangepicker').minDate = picker.startDate.startOf('day');

					// Update the end date if it's currently before the new start date
					if (endingDatePicker.data('daterangepicker').startDate.isBefore(picker.startDate)) {
						endingDatePicker.data('daterangepicker').setStartDate(picker.startDate);
					}
				});
				// Function to check if a given date is a Saturday
				function isSaturday(date) {
					return date.getDay() === 6;
				}

				// Function to check if a given date is a Sunday
				function isSunday(date) {
					return date.getDay() === 0;
				}
				// Convert the string dates to JavaScript Date objects
				var startingDate = new Date(result['data'].starting_date);
				var endingDate = new Date(result['data'].ending_date);
				// Check if the type is 2 and there is a date range
				if (result['data'].type === "2" && startingDate <= endingDate) {
					var currentDate = new Date(startingDate);
					var hasSaturday = false;
					var hasSunday = false;

					// Loop through the date range day by day to find Saturdays and Sundays
					while (currentDate <= endingDate) {
						if (isSaturday(currentDate)) {
							hasSaturday = true;
							// If Saturday is found, check if there's also a Sunday
							if (isSunday(currentDate)) {
								hasSunday = true;
								break; // No need to continue searching if both Saturday and Sunday are found
							}
						} else if (isSunday(currentDate)) {
							hasSunday = true;
							// If Sunday is found, check if there's also a Saturday
							if (isSaturday(currentDate)) {
								hasSaturday = true;
								break; // No need to continue searching if both Saturday and Sunday are found
							}
						}
						// Move to the next day
						currentDate.setDate(currentDate.getDate() + 1);
					}

					// Set the checkboxes accordingly
					if (hasSaturday) {
						$("#sat_update").prop("checked", true);
					}
					if (hasSunday) {
						$("#sun_update").prop("checked", true);
					}
				}
				$("#remarks").val(result['data'].remarks);
				$("#remarks").trigger("change");
				$("#type").val(result['data'].type);
				$("#type").trigger("change");

				$("#apply4").val(result['data'].apply);
				$("#apply4").trigger("change");
				if (result['data'].apply == '1') {
					$('#department').removeClass('hidden');
					if (result['data'].department != '' && result['data'].department != null) {
						$("#department3").val(result['data'].department.split(','));
						$("#department3").trigger("change");
					}
				}
				if (result['data'].apply == '2') {
					$('#users').removeClass('hidden');
					if (result['data'].users != '' && result['data'].users != null) {
						$("#users3").val(result['data'].users.split(','));
						$("#users3").trigger("change");
					}
				}


				$("#modal-edit-holiday").trigger("click");

			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});


$("#modal-edit-holiday").fireModal({
	title: $("#modal-edit-holiday-part").data('title'),
	body: $("#modal-edit-holiday-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-holiday-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

//   //////////////////////////////////////////////////////////////////////
// device Config//////////////////////////////////////////////////////////
$(document).on('click', '.delete_device', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'device_config/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {

						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-device', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'device_config/get_device_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			// console.log(result);
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});

			if (result['error'] == false && result['data'] != '') {
				$("#update_id").val(result['data'][0].id);
				$("#device_ip").val(result['data'][0].device_ip);
				$("#port").val(result['data'][0].port);
				$("#device_name").val(result['data'][0].device_name);

				if (result['data'][0].users != null) {
					var permissionsArray = JSON.parse(result['data'][0].users);
					if (permissionsArray.length > 0) {
						$('#users').multiSelect('deselect_all');
						$('#users').multiSelect('select', permissionsArray);
						$('#users').multiSelect('refresh');
					}
				}
				console.log(result['data'][0].users);


				$("#modal-edit-device").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-device").fireModal({
	title: $("#modal-edit-device-part").data('title'),
	body: $("#modal-edit-device-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-device-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});
///////////////////////////////////////////////////////////////////////////////
// departments
$(document).on('click', '.delete_department', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'department/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {

						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-department', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'department/get_department_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});

			if (result['error'] == false && result['data'] != '') {
				$("#update_id").val(result['data'][0].id);
				$("#department_name").val(result['data'][0].department_name);

				$("#modal-edit-department").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-department").fireModal({
	title: $("#modal-edit-department-part").data('title'),
	body: $("#modal-edit-department-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-department-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

// ////////////////////////////////////////////////////////////////////////////
$(document).on('click', '.delete_timesheet', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'projects/delete_timesheet/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload()
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$("#modal-add-timesheet").fireModal({
	title: $("#modal-add-timesheet-part").data('title'),
	body: $("#modal-add-timesheet-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-timesheet-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});
$(document).on('click', '.modal-edit-timesheet', function (e) {
	e.preventDefault();

	let save_button = $(this);
	save_button.addClass('btn-progress');

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'projects/get_timesheet_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			save_button.removeClass('btn-progress');
			if (result['error'] == false && result['data'] != '') {

				$("#project_id").val(result['data'][0].project_id);
				$("#project_id").trigger("change");

				$.ajax({
					type: "POST",
					url: base_url + 'projects/get_tasks_by_project_id',
					data: "project_id=" + result['data'][0].project_id,
					dataType: "json",
					success: function (data) {
						var tasks = '';
						if (data['data']) {
							$.each(data['data'], function (key, val) {
								tasks += ' <option value="' + val.id + '" ' + (result['data'][0].task_id == val.id ? "selected" : "") + '>' + val.title + '</option>';
							});
						}
						$("#task_id").html(tasks);
					}
				});

				$("#update_id").val(result['data'][0].id);

				$("#user_id").val(result['data'][0].user_id);
				$("#user_id").trigger("change");

				var time24 = false;
				if (time_format_js == 'H:mm') {
					time24 = true;
				}

				$('#starting_time').daterangepicker({
					locale: { format: date_format_js + ' ' + time_format_js },
					singleDatePicker: true,
					timePicker: true,
					timePicker24Hour: time24,
					startDate: moment(new Date(result['data'][0].starting_time), date_format_js + ' ' + time_format_js),
				});
				$('#ending_time').daterangepicker({
					locale: { format: date_format_js + ' ' + time_format_js },
					singleDatePicker: true,
					timePicker: true,
					timePicker24Hour: time24,
					startDate: moment(new Date(result['data'][0].ending_time), date_format_js + ' ' + time_format_js),
				});

				$("#note").val(result['data'][0].note);

				$("#modal-edit-timesheet").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-timesheet").fireModal({
	title: $("#modal-edit-timesheet-part").data('title'),
	body: $("#modal-edit-timesheet-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload()
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-timesheet-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '#timer_btn', function (e) {
	e.preventDefault();
	var project_id = $(this).data("project_id");
	var task_id = $(this).data("task_id");
	var timesheet_id = $(this).data("timesheet_id");
	if ($(this).hasClass('bg-danger')) {
		$('#timer_btn').removeClass('bg-danger');
		$('#timer_btn').addClass('bg-success');
		$('#timer_btn').html(start_timer);
		$.ajax({
			type: "POST",
			url: base_url + 'projects/stop_timesheet_timer/',
			data: "task_id=" + task_id + "&id=" + timesheet_id,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					$('#timer_btn').attr('data-timesheet_id', '');
				} else {
					iziToast.error({
						title: result['message'],
						message: "",
						position: 'topRight'
					});
				}
			}
		});
	} else {
		$('#timer_btn').removeClass('bg-success');
		$('#timer_btn').addClass('bg-danger');
		$('#timer_btn').html(stop_timer);
		$.ajax({
			type: 'POST',
			url: base_url + "projects/create_timesheet",
			data: "project_id=" + project_id + "&task_id=" + task_id,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					$('#timer_btn').attr('data-timesheet_id', result['data']);
				} else {
					iziToast.error({
						title: result['message'],
						message: "",
						position: 'topRight'
					});
				}
			}
		});
	}

	$('#timesheet_list').bootstrapTable('refresh');
});

$(document).on('click', '#timesheet-tab', function () {
	$('#timesheet_list').bootstrapTable('refresh');
});

$(document).on('click', '.reject_payment_request', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: you_want_reject_this_offline_request_this_can_not_be_undo,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'invoices/reject-request/',
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.accept_payment_request', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: you_want_accept_this_offline_request_this_can_not_be_undo,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'invoices/accept-request/',
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.delete_invoice', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: you_want_to_delete_this_invoice,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'invoices/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							$('#invoices_list').bootstrapTable('refresh');
							location.reload()
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-invoices', function (e) {
	e.preventDefault();

	let save_button = $(this);
	save_button.addClass('btn-progress');

	var id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: base_url + 'invoices/ajax_get_invoices_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			save_button.removeClass('btn-progress');
			if (result['error'] == false) {

				if (result['data'][0].products_id != '' && result['data'][0].products_id != null) {
					$("#products_id").val(result['data'][0].products_id.split(','));
					$("#products_id").trigger("change");
				}

				$("#tax").val('');
				$("#tax").trigger("change");
				$("#update_items_id").val('');
				$("#update_items_id").trigger('change');
				$.ajax({
					type: "POST",
					url: base_url + 'projects/get_clients_projects/' + result['data'][0].to_id,
					data: "to_id=" + result['data'][0].to_id,
					dataType: "json",
					success: function (data) {
						var projects = '';
						if (data['data']) {
							$.each(data['data'], function (key, val) {
								projects += ' <option value="' + val.id + '">' + val.title + '</option>';
							});
							$("#update_items_id").html(projects);
							$("#update_items_id").val(result['data'][0].items_id.split(','));
							$("#update_items_id").trigger('change');
						}
					}
				});
				$("#update_id").val(result['data'][0].id);

				$('#invoice_date').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: moment(result['data'][0].invoice_date, date_format_js),
				});
				$('#due_date').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: moment(result['data'][0].due_date, date_format_js),
				});

				$("#status").val(result['data'][0].status);
				$("#status").trigger("change");
				$("#to_id").val(result['data'][0].to_id);
				$("#to_id").trigger("change");

				if (result['data'][0].tax != '' && result['data'][0].tax != null) {
					$("#tax").val(result['data'][0].tax.split(','));
					$("#tax").trigger("change");
				}

				$("#note").val(result['data'][0].note);

				$("#modal-edit-invoices").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-invoices").fireModal({
	size: 'modal-lg',
	title: $("#modal-edit-invoices-part").data('title'),
	body: $("#modal-edit-invoices-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					$('#invoices_list').bootstrapTable('refresh');
					modal.modal('hide');
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-invoices-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '.delete_tax', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: you_want_to_delete_this_tax,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'settings/delete-taxes/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							$('#taxes_list').bootstrapTable('refresh');
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$("#taxes-form").submit(function (e) {
	e.preventDefault();
	let save_button = $(this).find('.savebtn'),
		output_status = $(this).find('.result'),
		card = $('#taxes-card');

	let card_progress = $.cardProgress(card, {
		spinner: true
	});
	save_button.addClass('btn-progress');
	output_status.html('');

	var formData = new FormData(this);
	$.ajax({
		type: 'POST',
		url: $(this).attr('action'),
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (result) {
			if (result['error'] == false) {
				$('#taxes_list').bootstrapTable('refresh');
				output_status.prepend('<div class="alert alert-success">' + result['message'] + '</div>');
				$("#update_id").val('');
				$("#title").val('');
				$("#tax").val('');
				$("#tax_cancel").addClass('d-none');
				$(".savebtn").html(create);
			} else {
				output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
			}
			output_status.find('.alert').delay(4000).fadeOut();
			save_button.removeClass('btn-progress');
			card_progress.dismiss(function () {
				$('html, body').animate({
					scrollTop: output_status.offset().top
				}, 1000);
			});
		}
	});
});

$(document).on('click', '#tax_cancel', function (e) {
	$("#update_id").val('');
	$("#title").val('');
	$("#tax").val('');
	$(".savebtn").html(create);
	$("#tax_cancel").addClass('d-none');
});

$(document).on('click', '.edit_tax', function (e) {
	e.preventDefault();

	let save_button = $(this);
	save_button.addClass('btn-progress');

	var id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: base_url + 'settings/get_taxes/' + id,
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			save_button.removeClass('btn-progress');
			if (result[0].id) {
				$("#update_id").val(result[0].id);
				$("#title").val(result[0].title);
				$("#tax").val(result[0].tax);
				$(".savebtn").html(update);
				$("#tax_cancel").removeClass('d-none');
				$('html, body').animate({
					scrollTop: 0
				}, 1000);
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$(document).on('change', '#invoices_client', function () {
	$("#items_id").prop('disabled', false);
	$.ajax({
		type: "POST",
		url: base_url + 'projects/get_clients_projects/' + $(this).val(),
		data: "to_id=" + $(this).val(),
		dataType: "json",
		success: function (result) {
			var projects = '';
			$.each(result['data'], function (key, val) {
				projects += ' <option value="' + val.id + '">' + val.title + '</option>';
			});
			$("#items_id").html(projects);
		}
	});
});

$("#modal-add-invoices").fireModal({
	size: 'modal-lg',
	title: $("#modal-add-invoices-part").data('title'),
	body: $("#modal-add-invoices-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-invoices-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '.delete_language', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	if (id == 1) {
		swal({
			title: wait,
			text: default_language_can_not_be_deleted,
			icon: 'info',
			dangerMode: true,
		});
	} else {
		swal({
			title: are_you_sure,
			text: you_want_to_delete_this_language,
			icon: 'warning',
			dangerMode: true,
			buttons: true,
			buttons: [cancel, ok]
		})
			.then((willDelete) => {
				if (willDelete) {
					$.ajax({
						type: "POST",
						url: base_url + 'Languages/delete/' + id,
						data: "id=" + id,
						dataType: "json",
						success: function (result) {
							if (result['error'] == false) {
								$('#languages_list').bootstrapTable('refresh');
							} else {
								iziToast.error({
									title: result['message'],
									message: "",
									position: 'topRight'
								});
							}
						}
					});
				}
			});
	}
});

$(document).on('click', '.delete_notification', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: you_want_to_delete_this_notification,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'notifications/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							$('#notifications_list').bootstrapTable('refresh');
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.delete_feature', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: you_want_to_delete_this_feature,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'front/delete-feature/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$("#feature-form").submit(function (e) {
	e.preventDefault();
	let save_button = $(this).find('.savebtn'),
		output_status = $(this).find('.result'),
		card = $('#feature-card');

	let card_progress = $.cardProgress(card, {
		spinner: true
	});
	save_button.addClass('btn-progress');
	output_status.html('');

	var formData = new FormData(this);
	$.ajax({
		type: 'POST',
		url: $(this).attr('action'),
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (result) {
			if (result['error'] == false) {
				location.reload();
			} else {
				output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
			}
			output_status.find('.alert').delay(4000).fadeOut();
			save_button.removeClass('btn-progress');
			card_progress.dismiss(function () {
				$('html, body').animate({
					scrollTop: output_status.offset().top
				}, 1000);
			});
		}
	});
});

$(document).on('click', '#home', function (e) {

	if ($('#home_div').hasClass('d-none')) {
		$('#home_div').removeClass('d-none');
	} else {
		$('#home_div').addClass('d-none');
	}
});

$(document).on('click', '#features', function (e) {

	if ($('#feature_div').hasClass('d-none')) {
		$('#feature_div').removeClass('d-none');
	} else {
		$('#feature_div').addClass('d-none');
	}
});

$(function () {
	$('.home-menu').click(function () {
		if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
			var target = $(this.hash);
			target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
			if (target.length) {
				$('html,body').animate({
					scrollTop: target.offset().top
				}, 1000);
				$("body").removeClass("sidebar-show");
				$("body").addClass("sidebar-gone");
				return false;
			}
		}
	});
});

$(document).on('click', '.reject_request', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: you_want_reject_this_offline_request_this_can_not_be_undo,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'plans/reject-request/',
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.accept_request', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	var plan_id = $(this).data("plan_id");
	var saas_id = $(this).data("saas_id");
	swal({
		title: are_you_sure,
		text: you_want_accept_this_offline_request_this_can_not_be_undo,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'plans/accept-request/',
					data: "id=" + id + "&plan_id=" + plan_id + "&saas_id=" + saas_id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-plan', function (e) {
	e.preventDefault();

	let save_button = $(this);
	save_button.addClass('btn-progress');

	var id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: base_url + 'plans/ajax_get_plan_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			save_button.removeClass('btn-progress');
			if (result['error'] == false) {

				$('input:checkbox').prop("checked", false);
				if (result['data'][0].modules != '') {
					$.each(JSON.parse(result['data'][0].modules), function (key, val) {
						if (val == 1) {
							$('#' + key + '_update').prop("checked", true).val(val);
							$('#' + key + '_module_update').prop("checked", true).val(val);
						}
					});
				}

				$("#update_id").val(result['data'][0].id);
				$("#title").val(result['data'][0].title);
				$("#price").val(result['data'][0].price);
				$("#billing_type").val(result['data'][0].billing_type);
				$("#billing_type").trigger("change");
				$("#projects").val(result['data'][0].projects);
				$("#tasks").val(result['data'][0].tasks);
				$("#users").val(result['data'][0].users);
				$("#storage").val(result['data'][0].storage);
				$("#modal-edit-plan").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-plan").fireModal({
	title: $("#modal-edit-plan-part").data('title'),
	size: 'modal-lg',
	body: $("#modal-edit-plan-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					$('#plans_list').bootstrapTable('refresh');
					modal.modal('hide');
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-plan-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-plan").fireModal({
	title: $("#modal-add-plan-part").data('title'),
	size: 'modal-lg',
	body: $("#modal-add-plan-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					$('#plans_list').bootstrapTable('refresh');
					modal.modal('hide');
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-plan-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '.delete_plan', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	if (id == 1) {
		swal({
			title: wait,
			text: default_plan_can_not_be_deleted,
			icon: 'info',
			dangerMode: true,
		});
	} else {
		swal({
			title: are_you_sure,
			text: you_want_to_delete_this_plan_all_users_under_this_plan_will_be_added_to_the_default_plan,
			icon: 'warning',
			dangerMode: true,
			buttons: true,
			buttons: [cancel, ok]
		})
			.then((willDelete) => {
				if (willDelete) {
					$.ajax({
						type: "POST",
						url: base_url + 'plans/delete/' + id,
						data: "id=" + id,
						dataType: "json",
						success: function (result) {
							if (result['error'] == false) {
								$('#plans_list').bootstrapTable('refresh');
							} else {
								iziToast.error({
									title: result['message'],
									message: "",
									position: 'topRight'
								});
							}
						}
					});
				}
			});
	}
});

$(document).on('click', '.check-todo', function (e) {

	if ($(this).hasClass('checked')) {
		$(this).removeClass('checked');
		$(this).children('strong').removeClass('text-primary text-strike');
		$(this).parent('.custom-checkbox').children('.text-small').addClass('text-muted').removeClass('text-success text-danger');
		var status = 0;
	} else {
		$(this).addClass('checked');
		$(this).children('strong').addClass('text-primary text-strike');
		$(this).parent('.custom-checkbox').children('.text-small').addClass('text-success').removeClass('text-muted text-danger');
		var status = 1;
	}
	var id = $(this).data("id");

	$.ajax({
		type: "POST",
		url: base_url + 'todo/update_status',
		data: "id=" + id + "&status=" + status,
		dataType: "json",
		success: function (result) {
			if (result['error'] == false) {

			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});


$(document).on('click', '.delete_todo', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: you_want_to_delete_this_todo,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'todo/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-todo', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'todo/get_todo',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});

			if (result['error'] == false) {
				$("#update_id").val(result['data'][0].id);
				$("#todo").val(result['data'][0].todo);
				$('#due_date').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: moment(result['data'][0].due_date, date_format_js),
				});
				$("#modal-edit-todo").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-todo").fireModal({
	title: $("#modal-edit-todo-part").data('title'),
	body: $("#modal-edit-todo-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-todo-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-todo").fireModal({
	title: $("#modal-add-todo-part").data('title'),
	body: $("#modal-add-todo-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-todo-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '.delete_notes', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: you_want_to_delete_this_note,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'notes/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-notes', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'notes/get_notes',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});

			if (result['error'] == false) {
				$("#update_id").val(result['data'][0].id);
				$("#description").val(result['data'][0].description);
				$("#modal-edit-notes").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-notes").fireModal({
	title: $("#modal-edit-notes-part").data('title'),
	body: $("#modal-edit-notes-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-notes-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-notes").fireModal({
	title: $("#modal-add-notes-part").data('title'),
	body: $("#modal-add-notes-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-notes-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '.delete_project', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: you_want_to_delete_this_project_all_related_data_with_this_project_also_will_be_deleted,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'projects/delete_project/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.delete_task', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		text: you_want_to_delete_this_task_all_related_data_with_this_task_also_will_be_deleted,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'projects/delete_task/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$("#setting-update-form").submit(function (e) {
	e.preventDefault();
	swal({
		title: are_you_sure,
		text: you_want_to_upgrade_the_system_please_take_a_backup_before_going_further,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	}).then((willDelete) => {
		if (willDelete) {
			let save_button = $(this).find('.savebtn'),
				output_status = $(this).find('.result'),
				card = $('#settings-card');

			let card_progress = $.cardProgress(card, {
				spinner: true
			});
			save_button.addClass('btn-progress');
			output_status.html('');

			var formData = new FormData(this);
			$.ajax({
				type: 'POST',
				url: $(this).attr('action'),
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				dataType: "json",
				success: function (result) {
					if (result['error'] == true) {
						output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
					} else {
						window.location.replace(base_url + "settings/migrate");
					}
					output_status.find('.alert').delay(4000).fadeOut();
					save_button.removeClass('btn-progress');
					card_progress.dismiss(function () {
						$('html, body').animate({
							scrollTop: output_status.offset().top
						}, 1000);
					});
				}
			});
		}
	});
});

$(document).on('click', '#comments-tab', function () {
	$("#is_comment").val('true');
	$("#is_attachment").val('false');
});

$(document).on('click', '#attachments-tab', function () {
	$("#is_comment").val('false');
	$("#is_attachment").val('true');
	$('#file_list').bootstrapTable('refresh');
});

$(document).on('change', '#project_id', function () {
	$.ajax({
		type: "POST",
		url: base_url + 'projects/get_project_users/' + $(this).val(),
		dataType: "json",
		success: function (result) {
			var user = '';
			$.each(result, function (key, val) {
				user += ' <option value="' + val.id + '">' + val.full_name + '</option>';
			});
			$("#users_append").html(user);
		}
	});
});

$(document).on('click', '.delete_files', function (e) {
	e.preventDefault();
	var url = $(this).data('delete');
	swal({
		title: are_you_sure,
		text: you_want_to_delete_this_file,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: url,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							$('#file_list').bootstrapTable('refresh');
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.task_search', function (e) {
	var value = $("#task_search_value").val();
	window.location.replace(base_url + "projects/tasks?search=" + value);
});

$(document).on('change', '.project_filter', function (e) {
	var value = $(this).val();
	window.location.replace(value);
});

$(document).on('change', '#date_format', function (e) {
	var js_value = $(this).find(':selected').data('js_value');
	$('#date_format_js').val(js_value);
});

$(document).on('change', '#time_format', function (e) {
	var js_value = $(this).find(':selected').data('js_value');
	$('#time_format_js').val(js_value);
});

$("#profile-form").submit(function (e) {
	e.preventDefault();
	let save_button = $(this).find('.savebtn'),
		output_status = $(this).find('.result'),
		card = $('#profile-card');

	let card_progress = $.cardProgress(card, {
		spinner: true
	});
	save_button.addClass('btn-progress');
	output_status.html('');

	var formData = new FormData(this);
	$.ajax({
		type: 'POST',
		url: $(this).attr('action'),
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (result) {
			if (result['error'] == false) {
				location.reload()
			} else {
				output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
				output_status.find('.alert').delay(4000).fadeOut();
				save_button.removeClass('btn-progress');
				card_progress.dismiss(function () {
					$('html, body').animate({
						scrollTop: output_status.offset().top
					}, 1000);
				});
			}
			card_progress.dismiss(function () {
			});
		}
	});
});

$(document).on('click', '#user_login_btn', function (e) {
	e.preventDefault();
	var id = $("#update_id").val();
	swal({
		title: are_you_sure,
		text: you_will_be_logged_out_from_the_current_account,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'auth/login_as_admin',
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '#user_delete_btn', function (e) {
	e.preventDefault();
	var id = $("#update_id").val();
	swal({
		title: are_you_sure,
		text: you_want_to_delete_this_user_all_related_data_with_this_user_also_will_be_deleted,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'auth/delete_user',
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '#user_active_btn', function (e) {
	e.preventDefault();
	var id = $("#update_id").val();
	swal({
		title: are_you_sure,
		text: you_want_to_activate_this_user,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'auth/activate',
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '#user_deactive_btn', function (e) {
	e.preventDefault();
	var id = $("#update_id").val();
	swal({
		title: are_you_sure,
		text: you_want_to_deactivate_this_user_this_user_will_be_not_able_to_login_after_deactivation,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'auth/deactivate',
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.edit_pages', function (e) {
	e.preventDefault();

	let save_button = $(this);
	save_button.addClass('btn-progress');

	var id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: base_url + 'front/get_pages/' + id,
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			save_button.removeClass('btn-progress');
			if (result[0].id) {
				$("#update_id").val(result[0].id);
				$("#content").val(result[0].content);
				$("#modal-edit-pages").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-pages").fireModal({
	title: $("#modal-edit-pages-part").data('title'),
	body: $("#modal-edit-pages-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-pages-part").data('btn_update'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '.modal-edit-user', function (e) {
	e.preventDefault();

	let save_button = $(this);
	save_button.addClass('btn-progress');

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'users/ajax_get_user_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			save_button.removeClass('btn-progress');
			console.log(result);

			if (result['error'] == false) {
				var dob = moment(result['data'].DOB, 'YYYY-MM-DD').format(date_format_js);

				var joinDate = result['data'].join_date;
				var joinDateMoment;

				// Check if the join date is in the format 'YYYY-MM-DD'
				if (/^\d{4}-\d{2}-\d{2}$/.test(joinDate)) {
					joinDateMoment = moment(joinDate, 'YYYY-MM-DD').format(date_format_js);
				} else {
					joinDateMoment = moment(joinDate, date_format_js);
				}

				$('#join_date').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: joinDateMoment,
				});

				$('#date_of_birth').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: dob,
				});
				$('#end_date').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: moment(result['data'].current_plan_expiry, date_format_js),
				});
				$("#update_id").val(result['data'].id);
				$("#employee_id").val(result['data'].employee_id);
				$("#company_name").val(result['data'].company);
				$("#old_profile_pic").val(result['data'].profile);
				$("#first_name").val(result['data'].first_name);
				$("#father_name").val(result['data'].father_name);
				$("#last_name").val(result['data'].last_name);
				$("#phone").val(result['data'].phone == 0 ? '' : result['data'].phone);

				$("#plan_id").val(result['data'].current_plan_id);
				$("#plan_id").trigger("change");
				$("#groups").val(result['data'].group_id);
				$("#groups").trigger("change");
				$("#email").val(result['data'].email);
				$("#email").trigger("change");
				$("#cnic").val(result['data'].cnic);
				$("#cnic").trigger("change");
				$("#gender").val(result['data'].gender);
				$("#gender").trigger("change");
				$("#desgnation").val(result['data'].desgnation);
				$("#desgnation").trigger("change");
				$("#department").val(result['data'].department);
				$("#department").trigger("change");
				$("#emg_person").val(result['data'].emg_person);
				$("#emg_person").trigger("change");
				$("#emg_number").val(result['data'].emg_number);
				$("#emg_number").trigger("change");
				var deviceArray = result['data'].device_id;
				if (deviceArray) {
					var parsedArray = JSON.parse(deviceArray);
					var deviceNumber = parseInt(parsedArray[0]);
					$("#device").val(deviceNumber);
					$("#device").trigger("change");
				}
				$("#address").val(result['data'].address);
				$("#address").trigger("change");
				$("#type").val(result['data'].type);
				$("#type").trigger("change");
				$("#active").val(result['data'].active);
				$("#active").trigger("change");
				if (result['data'].active == 1) {
					$("#user_deactive_btn").removeClass('d-none');
					$("#user_active_btn").addClass('d-none');
				} else {
					$("#user_deactive_btn").addClass('d-none');
					$("#user_active_btn").removeClass('d-none');
				}
				$("#modal-edit-user").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-user").fireModal({
	title: $("#modal-edit-user-part").data('title'),
	body: $("#modal-edit-user-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-user-part").data('btn_login'),
			submit: false,
			class: 'btn btn-warning',
			id: 'user_login_btn',
			handler: function (modal) {
			}
		},
		{
			text: $("#modal-edit-user-part").data('btn_delete'),
			submit: false,
			class: 'btn btn-danger',
			id: 'user_delete_btn',
			handler: function (modal) {
			}
		},
		{
			text: $("#modal-edit-user-part").data('btn_deactive'),
			submit: false,
			class: 'btn btn-danger d-none',
			id: 'user_deactive_btn',
			handler: function (modal) {
			}
		},

		{
			text: $("#modal-edit-user-part").data('btn_active'),
			submit: false,
			class: 'btn btn-success d-none',
			id: 'user_active_btn',
			handler: function (modal) {
			}
		},
		{
			text: $("#modal-edit-user-part").data('btn_update'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-user").fireModal({
	title: $("#modal-add-user-part").data('title'),
	body: $("#modal-add-user-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-user-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	],
	onFormReady: function (modal, form) {
		getEmployeeId();
	}
});

var submit_once = 0;
$("#modal-task-detail").fireModal({
	title: $("#modal-task-detail-part").data('title'),
	size: 'modal-lg',
	body: $("#modal-task-detail-part"),
	onFormSubmit: function (modal, e, form) {
		e.preventDefault();
		submit_once++;
		if (submit_once == 1) {
			var formData = new FormData(this);
			$.ajax({
				type: 'POST',
				url: $(this).attr('action'),
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				dataType: "json",
				success: function (result) {
					submit_once = 0;
					if (result['error'] == false) {
						if ($("#is_attachment").val() == 'true') {
							$("#attachment").val('');
							$('#file_list').bootstrapTable('refresh');
						}
						if ($("#is_comment").val() == 'true') {
							$('#message').val('');
							$.ajax({
								type: "POST",
								url: base_url + 'projects/get_comments',
								data: "type=task_comment&to_id=" + $('#comment_task_id').val(),
								dataType: "json",
								success: function (result_1) {
									if (result_1['error'] == false) {
										var html = '';
										var profile = '';
										$.each(result_1['data'], function (key, val) {
											if (val.profile) {
												var file_upload_path = '';
												if (file_exists(base_url + 'assets/uploads/profiles/' + val.profile)) {
													file_upload_path = base_url + 'assets/uploads/profiles/' + val.profile;
												} else {
													file_upload_path = base_url + 'assets/uploads/f' + saas_id + '/profiles/' + val.profile;
												}
												profile = '<figure class="avatar avatar-md mr-3">' +
													'<img src="' + file_upload_path + '" alt="' + val.first_name + ' ' + val.last_name + '">' +
													'</figure>';
											} else {
												profile = '<figure class="avatar avatar-md bg-primary text-white mr-3" data-initial="' + val.short_name + '"></figure>';
											}
											var can_delete = '';
											if (val.can_delete) {
												can_delete = '<div class="float-right text-primary"><a href="#" class="btn btn-icon btn-sm btn-danger delete_comment" data-id="' + val.id + '" data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i></a></div>';
											}
											html += '<ul class="list-unstyled list-unstyled-border mt-3">' +
												'<li class="media">' + profile +
												'<div class="media-body">' +
												'<div class="float-right text-primary">' + val.created + '</div>' +
												'<div class="media-title">' + val.first_name + ' ' + val.last_name + '</div>' + can_delete +
												'<span class="text-muted">' + val.message + '</span>' +
												'</div>' +
												'</li>' +
												'</ul>';
										});
										$("#comments_append").html(html);
									}
								}
							});
						}
					} else {
						modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
					}

					modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
					form.stopProgress();
				}
			});
		}
	},
	buttons: [
		{
			text: start_timer,
			submit: false,
			class: 'btn',
			id: 'timer_btn',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '.modal-task-detail', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'projects/get_tasks',
		data: "task_id=" + id,
		dataType: "json",
		success: function (result) {

			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});

			if (result['error'] == false) {


				$("#comments_append").html();

				if (result['data'][0]['can_see_time'] && is_module_allowed_timesheet) {
					$('#timer_btn').removeClass('d-none');
					$('#timer_btn').attr('data-project_id', result['data'][0]['project_id']);
					$('#timer_btn').attr('data-task_id', result['data'][0]['id']);
					$('#timer_btn').attr('data-timesheet_id', result['data'][0]['timer_running_id']);
				} else {
					$('#timer_btn').addClass('d-none');
				}

				if (result['data'][0]['timer_running']) {
					$('#timer_btn').removeClass('bg-success');
					$('#timer_btn').addClass('bg-danger');
					$('#timer_btn').html(stop_timer);
				} else {
					$('#timer_btn').removeClass('bg-danger');
					$('#timer_btn').addClass('bg-success');
					$('#timer_btn').html(start_timer);
				}

				$("#task_title").html(result['data'][0]['title']).removeClass().addClass('text-' + result['data'][0]['task_class']);
				$("#comment_task_id").val(result['data'][0]['id']);
				$("#attachment_task_id").val(result['data'][0]['id']);
				$("#task_project").html(result['data'][0]['project_title']).attr('href', base_url + 'projects/detail/' + result['data'][0]['project_id']);
				$("#task_description").html(result['data'][0]['description']);

				if (result['data'][0]['status'] == 4) {
					$("#task_days_count").html(completed);
				} else if (result['data'][0]['status'] == 3) {
					$("#task_days_count").html(result['data'][0]['review_days_count'] + ' ' + days + ' In Review');
				} else {
					$("#task_days_count").html(result['data'][0]['days_count'] + ' ' + days + ' ' + result['data'][0]['days_status']);
				}

				$("#task_due_date").html(result['data'][0]['due_date']);
				$("#task_starting_date").html(result['data'][0]['starting_date']);
				$("#task_priority").html(result['data'][0]['task_priority']).removeClass().addClass('text-' + result['data'][0]['priority_class']);

				var profile_1 = '';
				$.each(result['data'][0]['task_users'], function (key, val) {
					if (val.profile) {

						var file_upload_path = '';
						if (file_exists(base_url + 'assets/uploads/profiles/' + val.profile)) {
							file_upload_path = base_url + 'assets/uploads/profiles/' + val.profile;
						} else {
							file_upload_path = base_url + 'assets/uploads/f' + saas_id + '/profiles/' + val.profile;
						}

						profile_1 += '<figure class="avatar avatar-sm mr-1">' +
							'<img src="' + file_upload_path + '" alt="' + val.first_name + ' ' + val.last_name + '" data-toggle="tooltip" data-placement="top" title="' + val.first_name + ' ' + val.last_name + '">' +
							'</figure>';
					} else {
						profile_1 += '<figure class="avatar avatar-sm bg-primary text-white mr-1" data-initial="' + val.first_name.charAt(0) + '' + val.last_name.charAt(0) + '" data-toggle="tooltip" data-placement="top" title="' + val.first_name + ' ' + val.last_name + '"></figure>';
					}
				});

				$("#task_users").html(profile_1);

				$("#modal-task-detail").trigger("click");

				$("#comments-tab").trigger("click");

				$.ajax({
					type: "POST",
					url: base_url + 'projects/get_comments',
					data: "type=task_comment&to_id=" + result['data'][0]['id'],
					dataType: "json",
					success: function (result_1) {
						if (result_1['error'] == false) {
							var html = '';
							var profile = '';
							$.each(result_1['data'], function (key, val) {

								if (val.profile) {
									var file_upload_path = '';
									if (file_exists(base_url + 'assets/uploads/profiles/' + val.profile)) {
										file_upload_path = base_url + 'assets/uploads/profiles/' + val.profile;
									} else {
										file_upload_path = base_url + 'assets/uploads/f' + saas_id + '/profiles/' + val.profile;
									}

									profile = '<figure class="avatar avatar-md mr-3">' +
										'<img src="' + file_upload_path + '" alt="' + val.first_name + ' ' + val.last_name + '">' +
										'</figure>';
								} else {
									profile = '<figure class="avatar avatar-md bg-primary text-white mr-3" data-initial="' + val.short_name + '"></figure>';
								}
								var can_delete = '';
								if (val.can_delete) {
									can_delete = '<div class="float-right text-primary"><a href="#" class="btn btn-icon btn-sm btn-danger delete_comment" data-id="' + val.id + '" data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i></a></div>';
								}
								html += '<ul class="list-unstyled list-unstyled-border mt-3">' +
									'<li class="media">' + profile +
									'<div class="media-body">' +
									'<div class="float-right text-primary">' + val.created + '</div>' +
									'<div class="media-title">' + val.first_name + ' ' + val.last_name + '</div>' + can_delete +
									'<span class="text-muted">' + val.message + '</span>' +
									'</div>' +
									'</li>' +
									'</ul>';
							});
							$("#comments_append").html(html);
						}
					}
				});

			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$(document).on('click', '.delete_comment', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	$(this).closest('li').remove();
	$.ajax({
		type: 'POST',
		url: base_url + "projects/delete_task_comment/" + id,
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			if (result['error'] == true) {
				modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
			}
			modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
		}
	});
});

$(document).on('click', '.modal-edit-task', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'projects/get_tasks',
		data: "task_id=" + id,
		dataType: "json",
		success: function (result) {

			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});

			if (result['error'] == false) {
				$("#update_id").val(id);
				$("#title").val(result['data'][0]['title']);
				$("#description").val(result['data'][0]['description']);

				$('#due_date').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: moment(result['data'][0]['due_date'], date_format_js),
				});

				if (result['data'][0]['starting_date']) {
					$('#starting_date').daterangepicker({
						locale: { format: date_format_js },
						singleDatePicker: true,
						startDate: moment(result['data'][0]['starting_date'], date_format_js),
					});
				}


				$("#status").val(result['data'][0]['status']);
				$("#status").trigger("change");
				$("#priority").val(result['data'][0]['priority']);
				$("#priority").trigger("change");
				if (result['data'][0]['task_users_ids'] != '' && result['data'][0]['task_users_ids'] != null) {
					result['data'][0]['task_users_ids'] = result['data'][0]['task_users_ids'].split(',');
					$("#users").val(result['data'][0]['task_users_ids']);
					$("#users").trigger('change');
				}
				$("#modal-edit-task").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-task").fireModal({
	title: $("#modal-edit-task-part").data('title'),
	body: $("#modal-edit-task-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-task-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-task").fireModal({
	title: $("#modal-add-task-part").data('title'),
	body: $("#modal-add-task-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-task-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '.modal-edit-project', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'projects/get_projects',
		data: "project_id=" + id,
		dataType: "json",
		success: function (result) {
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});

			if (result['error'] == false && result['data'][0]['id'] != undefined) {
				$("#update_id").val(id);
				$("#title").val(result['data'][0]['title']);
				$("#description").val(result['data'][0]['description']);

				$('#starting_date').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: moment(result['data'][0]['starting_date'], date_format_js),
				});
				var presentValue = result['data'][0]['ending_date'];
				if (presentValue === 'Present') {
					$('#ending_date').daterangepicker({
						locale: { format: date_format_js },
						singleDatePicker: true,
						startDate: moment(), // Set the startDate to the current date
					});
				}
				else {
					$('#ending_date').daterangepicker({
						locale: { format: date_format_js },
						singleDatePicker: true,
						startDate: moment(result['data'][0]['ending_date'], date_format_js),
					});
				}
				$("#budget").val(result['data'][0]['budget']);
				$("#status").val(result['data'][0]['status']);

				$("#status").trigger("change");
				if (result['data'][0]['project_users_ids'] != '' && result['data'][0]['project_users_ids'] != null) {
					result['data'][0]['project_users_ids'] = result['data'][0]['project_users_ids'].split(',');
				}
				$("#users").val(result['data'][0]['project_users_ids']);
				$("#users").trigger('change');
				$("#client").val(result['data'][0]['client_id']);
				$("#client").trigger('change');
				var present = result['data'][0]['present'];
				if (present === '1') {
					$('#present_edit').prop('checked', true);
					$('#ending_date').hide();
					$('#present_edit_input').show();
				} else {
					$('#present_edit').prop('checked', false);
					$('#ending_date').show();
					$('#present_edit_input').hide();
				}
				$("#modal-edit-project").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});


$("#modal-edit-project").fireModal({
	title: $("#modal-edit-project-part").data('title'),
	body: $("#modal-edit-project-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-project-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-project").fireModal({
	title: $("#modal-add-project-part").data('title'),
	body: $("#modal-add-project-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-project-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});


$("#language-form").submit(function (e) {
	e.preventDefault();
	let save_button = $(this).find('.savebtn'),
		output_status = $(this).find('.result'),
		card = $('#language-card');

	let card_progress = $.cardProgress(card, {
		spinner: true
	});
	save_button.addClass('btn-progress');
	output_status.html('');

	var formData = new FormData(this);
	$.ajax({
		type: 'POST',
		url: $(this).attr('action'),
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (result) {
			if (result['error'] == false) {
				$('#languages_list').bootstrapTable('refresh');
				output_status.prepend('<div class="alert alert-success">' + result['message'] + '</div>');
			} else {
				output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
			}
			output_status.find('.alert').delay(4000).fadeOut();
			save_button.removeClass('btn-progress');
			card_progress.dismiss(function () {
				$('html, body').animate({
					scrollTop: output_status.offset().top
				}, 1000);
			});
		}
	});
});


$("#setting-form").submit(function (e) {
	e.preventDefault();
	let save_button = $(this).find('.savebtn'),
		output_status = $(this).find('.result'),
		card = $('#settings-card');

	let card_progress = $.cardProgress(card, {
		spinner: true
	});
	save_button.addClass('btn-progress');
	output_status.html('');

	var formData = new FormData(this);
	$.ajax({
		type: 'POST',
		url: $(this).attr('action'),
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (result) {
			if (result['error'] == false) {
				if (result['data']['full_logo'] != undefined && result['data']['full_logo'] != '') {
					$('#full_logo-img').attr('src', base_url + 'assets/uploads/logos/' + result['data']['full_logo']);
				}
				if (result['data']['half_logo'] != undefined && result['data']['half_logo'] != '') {
					$('#half_logo-img').attr('src', base_url + 'assets/uploads/logos/' + result['data']['half_logo']);
				}
				if (result['data']['favicon'] != undefined && result['data']['favicon'] != '') {
					$('#favicon-img').attr('src', base_url + 'assets/uploads/logos/' + result['data']['favicon']);
				}
				output_status.prepend('<div class="alert alert-success">' + result['message'] + '</div>');
			} else {
				output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
			}
			output_status.find('.alert').delay(4000).fadeOut();
			save_button.removeClass('btn-progress');
			card_progress.dismiss(function () {
				$('html, body').animate({
					scrollTop: output_status.offset().top
				}, 1000);
			});
		}
	});
});
$("#setting-form2").submit(function (e) {
	e.preventDefault();
	let save_button = $(this).find('.savebtn'),
		output_status = $(this).find('.result'),
		card = $('#settings-card');

	let card_progress = $.cardProgress(card, {
		spinner: true
	});
	save_button.addClass('btn-progress');
	output_status.html('');

	var formData = new FormData(this);
	$.ajax({
		type: 'POST',
		url: $(this).attr('action'),
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (result) {
			if (result['error'] == false) {
				output_status.prepend('<div class="alert alert-success">' + result['message'] + '</div>');
			} else {
				output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
			}
			output_status.find('.alert').delay(4000).fadeOut();
			save_button.removeClass('btn-progress');
			card_progress.dismiss(function () {
				$('html, body').animate({
					scrollTop: output_status.offset().top
				}, 1000);
			});
		}
	});
});

$("#home-form").submit(function (e) {
	e.preventDefault();
	let save_button = $(this).find('.savebtn'),
		output_status = $(this).find('.result'),
		card = $('#home-card');

	let card_progress = $.cardProgress(card, {
		spinner: true
	});
	save_button.addClass('btn-progress');
	output_status.html('');

	var formData = new FormData(this);
	$.ajax({
		type: 'POST',
		url: $(this).attr('action'),
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (result) {
			if (result['error'] == false) {
				output_status.prepend('<div class="alert alert-success">' + result['message'] + '</div>');
			} else {
				output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
			}
			output_status.find('.alert').delay(4000).fadeOut();
			save_button.removeClass('btn-progress');
			card_progress.dismiss(function () {
				$('html, body').animate({
					scrollTop: output_status.offset().top
				}, 1000);
			});
		}
	});
});
$(document).on('change', '#php_timezone', function (e) {
	var gmt = $(this).find(':selected').data('gmt');
	$('#mysql_timezone').val(gmt);
});

$("#modal-forgot-password").fireModal({
	title: $("#modal-forgot-password-part").data('title'),
	body: $("#modal-forgot-password-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					modal.find('.modal-body').append('<div class="alert alert-success">' + result['message'] + '</div>');
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-forgot-password-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

$("#register").submit(function (e) {
	e.preventDefault();
	$("input[name='token']").remove();
	var $this = $(this);
	let save_button = $(this).find('.savebtn'),
		output_status = $(this).find('.result'),
		card = $('#register');

	let card_progress = $.cardProgress(card, {
		spinner: true
	});
	save_button.addClass('btn-progress');
	output_status.html('');

	if (site_key) {
		grecaptcha.ready(function () {
			grecaptcha.execute(site_key, { action: 'register_form' }).then(function (token) {
				$($this).prepend('<input type="hidden" name="token" value="' + token + '">');
				$($this).prepend('<input type="hidden" name="action" value="register_form">');
				var formData = new FormData(document.getElementById("register"));
				$.ajax({
					type: 'POST',
					url: $($this).attr('action'),
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					dataType: "json",
					success: function (result) {
						console.log(result);
						card_progress.dismiss(function () {
							if (result['error'] == false) {
								window.location.replace(base_url + 'auth/confirmation');
							} else {
								output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
							}
							output_status.find('.alert').delay(4000).fadeOut();
							save_button.removeClass('btn-progress');
							$('html, body').animate({
								scrollTop: output_status.offset().top
							}, 1000);
						});
					}
				});
			});
		});
	} else {
		var formData = new FormData(document.getElementById("register"));
		$.ajax({
			type: 'POST',
			url: $($this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				card_progress.dismiss(function () {
					if (result['error'] == false) {
						window.location.replace(base_url + 'auth/confirmation');
					} else {
						output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
					}
					output_status.find('.alert').delay(4000).fadeOut();
					save_button.removeClass('btn-progress');
					$('html, body').animate({
						scrollTop: output_status.offset().top
					}, 1000);
				});
			}
		});
	}
	return false;
});


$(document).on('click', '.delete_biometric', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'biometric_missing/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-biometric', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'biometric_missing/get_biometric_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			var date = moment(result['data'][0].date, 'YYYY-MM-DD').format(date_format_js);
			var time = moment(result['data'][0].time, 'HH:mm:ss').format(time_format_js);
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});
			if (result['error'] == false && result['data'] != '') {
				$("#update_id").val(result['data'][0].id);
				$("#employee_id").val(result['data'][0].employee_id);
				$("#user_id").val(result['data'][0].user_id);
				$("#user_id").trigger("change");

				$('#date').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: date,
				});

				var time24 = true;
				$('#time').timepicker({
					format: 'HH:mm',
					showMeridian: false,
					time24Hour: time24
				});
				$('#time').timepicker('setTime', time); // Set the default starting 

				$("#reason").val(result['data'][0].reason);
				$("#reason").trigger("change");
				$("#status").val(result['data'][0].status);
				$("#status").trigger("change");
				var type = result['data'][0].type;
				if (type === 'check_in') {
					$('#check_in').prop('checked', true);
					$('#check_out').prop('checked', false);
				} else if (type === 'check_out') {
					$('#check_out').prop('checked', true);
					$('#check_in').prop('checked', false);
				}

				$("#modal-edit-biometric").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-biometric").fireModal({
	title: $("#modal-edit-biometric-part").data('title'),
	body: $("#modal-edit-biometric-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-biometric-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});
$("#login").submit(function (e) {
	e.preventDefault();
	let save_button = $(this).find('.savebtn'),
		output_status = $(this).find('.result'),
		card = $('#login');

	let card_progress = $.cardProgress(card, {
		spinner: true
	});
	save_button.addClass('btn-progress');
	output_status.html('');
	var formData = new FormData(this);
	$.ajax({
		type: 'POST',
		url: $(this).attr('action'),
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (result) {
			card_progress.dismiss(function () {
				if (result['error'] == false) {
					window.location.replace(base_url);
				} else {
					output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				output_status.find('.alert').delay(4000).fadeOut();
				save_button.removeClass('btn-progress');
				$('html, body').animate({
					scrollTop: output_status.offset().top
				}, 1000);
			});
		}
	});

	return false;
});
$(document).on('click', '.delete_biomteric', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'biometric_missing/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload();
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-biomteric', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	$.ajax({
		type: "POST",
		url: base_url + 'biometric_missing/get_biometric_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			var date = moment(result['data'][0].date, 'YYYY-MM-DD').format(date_format_js);
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});
			if (result['error'] == false && result['data'] != '') {
				$("#update_id").val(result['data'][0].id);
				$("#employee_id").val(result['data'][0].employee_id);
				$("#user_id").val(result['data'][0].user_id);
				$("#user_id").trigger("change");

				$('#starting_date').daterangepicker({
					locale: { format: date_format_js },
					singleDatePicker: true,
					startDate: startingDate,
				});

				var time24 = false;
				if (time_format_js == 'H:mm') {
					time24 = true;
				}

				$('#time').daterangepicker({
					locale: { format: time_format_js, },
					timePicker: true,
					timePickerIncrement: 1,
					timePicker24Hour: true,
					startDate: moment(result['data'][0].time, 'HH:mm').format(time_format_js),
				});

				$("reason").val(result['data'][0].leave_reason);
				$("#status").val(result['data'][0].status);
				$("#status").trigger("change");

				$("#modal-edit-biometric").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-biomteric").fireModal({
	title: $("#modal-edit-biomteric-part").data('title'),
	body: $("#modal-edit-biomteric-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-biomteric-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});


$("#modal-add-biometric").fireModal({
	title: $("#modal-add-biometric-part").data('title'),
	body: $("#modal-add-biometric-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});


		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-biometric-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});
/////////////////////////////////////////////////////////////////////////
// shifts//////////////////////////////////////////////////////////////
$(document).on('click', '.delete_shift', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'shift/delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload()
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-shift', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	;
	$.ajax({
		type: "POST",
		url: base_url + 'shift/get_shift_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			console.log(result);
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});
			if (result['error'] == false) {

				$("#update_id").val(result['data'].id);


				var starting_time = moment(result['data'].starting_time, 'HH:mm:ss').format(time_format_js);
				var ending_time = moment(result['data'].ending_time, 'HH:mm:ss').format(time_format_js);
				var break_start = moment(result['data'].break_start, 'HH:mm:ss').format(time_format_js);
				var break_end = moment(result['data'].break_end, 'HH:mm:ss').format(time_format_js);
				var half_day_check_out = moment(result['data'].half_day_check_out, 'HH:mm:ss').format(time_format_js);
				var half_day_check_in = moment(result['data'].half_day_check_in, 'HH:mm:ss').format(time_format_js);

				if (result['data'].users != null) {
					var usersArray = result['data'].users.split(',');

					if (usersArray.length > 0) {
						// Set the selected values in the multi-select
						$('#users').multiSelect('deselect_all');
						usersArray.forEach(function (value) {
							$('#users').multiSelect('select', value);
						});

						// Trigger the change event for the multi-select to reflect the changes
						$('#users').multiSelect('refresh');
					}
				}
				var time24 = true;
				$('#starting_time').timepicker({
					format: 'HH:mm',
					showMeridian: false,
					time24Hour: time24
				});
				$('#starting_time').timepicker('setTime', starting_time);

				$('#ending_time').timepicker({
					format: 'HH:mm',
					showMeridian: false,
					time24Hour: time24
				});

				$('#ending_time').timepicker('setTime', ending_time);

				$('#break_start').timepicker({
					format: 'HH:mm',
					showMeridian: false,
					time24Hour: time24
				});
				$('#break_start').timepicker('setTime', break_start);

				$('#break_end').timepicker({
					format: 'HH:mm',
					showMeridian: false,
					time24Hour: time24
				});
				$('#break_end').timepicker('setTime', break_end);

				$('#half_day_check_out').timepicker({
					format: 'HH:mm',
					showMeridian: false,
					time24Hour: time24
				});
				$('#half_day_check_out').timepicker('setTime', half_day_check_out);

				$('#half_day_check_in').timepicker({
					format: 'HH:mm',
					showMeridian: false,
					time24Hour: time24
				});
				$('#half_day_check_in').timepicker('setTime', half_day_check_in);

				$("#name").val(result['data'].name);
				$("#name").trigger("change");

				$("#modal-edit-shift").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-shift").fireModal({
	title: $("#modal-edit-shift-part").data('title'),
	body: $("#modal-edit-shift-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();

				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-shift-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-shift").fireModal({
	title: $("#modal-add-shift-part").data('title'),
	body: $("#modal-add-shift-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-shift-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-assign-shift").fireModal({
	title: $("#modal-add-assign-shift-part").data('title'),
	body: $("#modal-add-assign-shift-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-assign-shift-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});

$(document).on('click', '.delete_roles', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'settings/roles_delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload()
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
});

$(document).on('click', '.modal-edit-roles', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	;
	$.ajax({
		type: "POST",
		url: base_url + 'settings/get_roles_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});
			if (result['error'] == false) {

				$("#update_id").val(result['data'].id);

				$("#name").val(result['data'].name);
				$("#name").trigger("change");

				$("#description").val(result['data'].description);
				$("#description").trigger("change");

				$("#descriptive_name").val(result['data'].descriptive_name);
				$("#descriptive_name").trigger("change");

				if (result['data'].permissions != null) {
					var permissionsArray = JSON.parse(result['data'].permissions);

					if (permissionsArray !== null) {
						// Clear existing selections in the multi-select
						$('#permissions').multiSelect('deselect_all');

						// Select options in the multi-select based on the values in permissionsArray
						permissionsArray.forEach(function (value) {
							$('#permissions').multiSelect('select', value);
						});

						// Trigger the change event for the multi-select to reflect the changes
						$('#permissions').multiSelect('refresh');
					}
				}

				if (result['data'].assigned_users != null) {
					var permissionsArray2 = JSON.parse(result['data'].assigned_users);

					if (permissionsArray2 !== null) {
						// Clear existing selections in the multi-select
						$('#users').multiSelect('deselect_all');

						// Select options in the multi-select based on the values in permissionsArray2
						permissionsArray2.forEach(function (value) {
							$('#users').multiSelect('select', value);
						});

						// Trigger the change event for the multi-select to reflect the changes
						$('#users').multiSelect('refresh');
					}
				}


				var newArray = [];
				if (result['data'].change_permissions_of != '' && result['data'].change_permissions_of != null) {
					var inputString = result['data'].change_permissions_of.replace(/[\[\]"]+/g, ''); // Remove brackets and double quotes
					newArray = inputString.split(',');
				}

				$("#change_permissions_of").val(newArray);
				$("#change_permissions_of").trigger("change");

				$("#modal-edit-roles").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-roles").fireModal({
	title: $("#modal-edit-roles-part").data('title'),
	body: $("#modal-edit-roles-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					$('#role_list').bootstrapTable('refresh');
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-roles-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-roles").fireModal({
	title: $("#modal-add-roles-part").data('title'),
	body: $("#modal-add-roles-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-roles-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});


$(document).on('click', '.delete-leaves-type', function (e) {
	e.preventDefault();
	var id = $(this).data("id");
	swal({
		title: are_you_sure,
		icon: 'warning',
		dangerMode: true,
		buttons: true,
		buttons: [cancel, ok]
	})
		.then((willDelete) => {
			if (willDelete) {
				$.ajax({
					type: "POST",
					url: base_url + 'settings/leaves_type_delete/' + id,
					data: "id=" + id,
					dataType: "json",
					success: function (result) {
						if (result['error'] == false) {
							location.reload()
						} else {
							iziToast.error({
								title: result['message'],
								message: "",
								position: 'topRight'
							});
						}
					}
				});
			}
		});
}); 

$(document).on('click', '.modal-edit-leaves-type', function (e) {
	e.preventDefault();

	var card = $(this).closest('.card');
	let save_button = $(this);
	save_button.addClass('btn-progress');
	let card_progress = $.cardProgress(card, {
		spinner: true
	});

	var id = $(this).data("edit");
	;
	$.ajax({
		type: "POST",
		url: base_url + 'settings/get_leaves_type_by_id',
		data: "id=" + id,
		dataType: "json",
		success: function (result) {
			console.log(result);
			card_progress.dismiss(function () {
				save_button.removeClass('btn-progress');
			});
			if (result['error'] == false) {
				$("#update_id").val(result['data'].id);
				$("#name").val(result['data'].name);
				$("#name").trigger("change");
				$("#duration").val(result['data'].duration);
				$("#duration").trigger("change");
				$("#modal-edit-leaves-type").trigger("click");
			} else {
				iziToast.error({
					title: something_wrong_try_again,
					message: "",
					position: 'topRight'
				});
			}
		}
	});
});

$("#modal-edit-leaves-type").fireModal({
	title: $("#modal-edit-leaves-type-part").data('title'),
	body: $("#modal-edit-leaves-type-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();

				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-edit-leaves-type-part").data('btn'),
			submit: true,
			class: 'btn btn-primary',
			handler: function (modal) {
			}
		}
	]
});

$("#modal-add-leaves-type").fireModal({
	title: $("#modal-add-leaves-type-part").data('title'),
	body: $("#modal-add-leaves-type-part"),
	footerClass: 'bg-whitesmoke',
	autoFocus: false,
	onFormSubmit: function (modal, e, form) {
		var formData = new FormData(this);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (result) {
				if (result['error'] == false) {
					location.reload();
				} else {
					modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
				}
				modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
				form.stopProgress();
			}
		});

		e.preventDefault();
	},
	buttons: [
		{
			text: $("#modal-add-leaves-type-part").data('btn'),
			submit: true,
			class: 'btn btn-primary ',
			handler: function (modal) {
			}
		}
	]
});