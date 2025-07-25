"use strict";

$('.cookie-bar-btn').on('click', function () {
    localStorage.setItem('cookie-wap', '1');
    $('#cookie-bar').fadeOut();
});

if (localStorage.getItem('cookie-wap') != '1') {
    $('#cookie-bar').delay(2000).fadeIn();
}

$("#front_contact_form").submit(function (e) {
    e.preventDefault();
    $("input[name='token']").remove();
    var $this = $(this);
    let save_button = $(this).find('.savebtn'),
        output_status = $(this).find('.result');
    save_button.addClass('disabled opacity-50 cursor-not-allowed').prop('disabled', true);
    output_status.html('');
    if (site_key) {
        grecaptcha.ready(function () {
            grecaptcha.execute(site_key, { action: 'contact_form' }).then(function (token) {
                $($this).prepend('<input type="hidden" name="token" value="' + token + '">');
                $($this).prepend('<input type="hidden" name="action" value="contact_form">');
                var formData = new FormData(document.getElementById("front_contact_form"));
                output_status.show();
                $.ajax({
                    type: 'POST',
                    url: $($this).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (result) {
                        output_status.show();
                        if (result['error'] == false) {
                            output_status.prepend('<div class="alert alert-success">' + result['message'] + '</div>');
                        } else {
                            output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
                        }
                        output_status.delay(4000).fadeOut();
                        save_button.removeClass('disabled opacity-50 cursor-not-allowed').prop('disabled', false);
                    },
                    error: function (result) {
                        output_status.show();
                        output_status.prepend('Something went wrong. Try Again');
                        output_status.delay(4000).fadeOut();
                        save_button.removeClass('disabled opacity-50 cursor-not-allowed').prop('disabled', false);
                    }
                });
            });
        });
    } else {
        var formData = new FormData(document.getElementById("front_contact_form"));
        $.ajax({
            type: 'POST',
            url: $($this).attr('action'),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (result) {
                output_status.show();
                if (result['error'] == false) {
                    output_status.prepend('<div class="alert alert-success">' + result['message'] + '</div>');
                } else {
                    output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
                }
                output_status.delay(4000).fadeOut();
                save_button.removeClass('disabled opacity-50 cursor-not-allowed').prop('disabled', false);
            },
            error: function (result) {
                output_status.show();
                output_status.prepend('Something went wrong. Try Again');
                output_status.delay(4000).fadeOut();
                save_button.removeClass('disabled opacity-50 cursor-not-allowed').prop('disabled', false);
            }
        });
    }
});
$("#contact-form2").submit(function (e) {
    e.preventDefault();
    $("input[name='token']").remove();
    var $this = $(this);
    let save_button = $(this).find('.savebtn'),
        output_status = $(this).find('.ajax-response');
    save_button.addClass('disabled opacity-50 cursor-not-allowed').prop('disabled', true);
    output_status.html('');
    if (site_key) {
        grecaptcha.ready(function () {
            grecaptcha.execute(site_key, { action: 'contact_form' }).then(function (token) {
                $($this).prepend('<input type="hidden" name="token" value="' + token + '">');
                $($this).prepend('<input type="hidden" name="action" value="contact_form">');
                var formData = new FormData(document.getElementById("contact-form2"));
                output_status.show();
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
                        output_status.show();
                        if (result['error'] == false) {
                            output_status.prepend(result);
                        } else {
                            output_status.prepend(result['message']);
                        }
                        output_status.delay(4000).fadeOut();
                        save_button.removeClass('disabled opacity-50 cursor-not-allowed').prop('disabled', false);
                    },
                    error: function (result) {
                        output_status.show();
                        output_status.prepend('Something went wrong. Try Again2');
                        output_status.delay(4000).fadeOut();
                        save_button.removeClass('disabled opacity-50 cursor-not-allowed').prop('disabled', false);
                    }
                });
            });
        });
    } else {
        var formData = new FormData(document.getElementById("contact-form2"));
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
                output_status.show();
                if (result['error'] == false) {
                    output_status.prepend('<div class="alert alert-success">' + result['message'] + '</div>');
                } else {
                    output_status.prepend('<div class="alert alert-danger">' + result['message'] + '</div>');
                }
                output_status.delay(4000).fadeOut();
                save_button.removeClass('disabled opacity-50 cursor-not-allowed').prop('disabled', false);
            },
            error: function (result) {
                output_status.show();
                output_status.prepend('Something went wrong. Try Again');
                output_status.delay(4000).fadeOut();
                save_button.removeClass('disabled opacity-50 cursor-not-allowed').prop('disabled', false);
            }
        });
    }
});

