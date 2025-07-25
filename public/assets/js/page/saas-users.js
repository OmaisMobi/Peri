"use strict";


$("#saas-add-modal").on('click', '.btn-create-saas', function (e) {
  var modal = $('#saas-add-modal');
  var form = $('#modal-add-user-part');
  var formData = form.serialize();
  console.log(formData);

  $.ajax({
    type: 'POST',
    url: form.attr('action'),
    data: formData,
    dataType: "json",
    beforeSend: function () {
      $(".modal-body").append(ModelProgress);
    },
    success: function (result) {
      if (result['error'] == false) {
        location.reload();
      } else {
        modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>').find('.alert').delay(4000).fadeOut();
      }
    },
    complete: function () {
      $(".loader-progress").remove();
    }
  });

  e.preventDefault();
});
$("#saas-edit-modal").on('click', '.btn-save-saas', function (e) {
  var modal = $('#saas-edit-modal');
  var form = $('#modal-edit-saas-part');
  var formData = form.serialize();
  console.log(formData);

  $.ajax({
    type: 'POST',
    url: form.attr('action'),
    data: formData,
    dataType: "json",
    beforeSend: function () {
      $(".modal-body").append(ModelProgress);
    },
    success: function (result) {
      if (result['error'] == false) {
        location.reload();
      } else {
        modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>').find('.alert').delay(4000).fadeOut();
      }
    },
    complete: function () {
      $(".loader-progress").remove();
    }
  });

  e.preventDefault();
});

$('#tool').on('change', function (e) {
  $('#users_list').bootstrapTable('refresh');
});

$(document).on('click', '.btn-edit-saas', function (e) {
  e.preventDefault();
  var id = $(this).data("id");
  $.ajax({
    type: "POST",
    url: base_url + 'users/ajax_get_user_by_id',
    data: "id=" + id,
    dataType: "json",
    beforeSend: function () {
      $(".modal-body").append(ModelProgress);
    },
    success: function (result) {
      console.log(result);
      if (result['error'] == false && result['data'] != '') {
        $("#update_id").val(result['data'].id);
        $("#first_name").val(result['data'].first_name);
        $("#last_name").val(result['data'].last_name);
        $("#phone").val(result['data'].phone);

        var temp = '';
        temp += '<button type="button" class="btn btn-save-saas btn-primary">Save</button>';
        temp += '<button type="button" data-id="' + result['data'].id + '" class="btn btn-login-saas btn-warning ms-2">Login</button>';
        temp += '<button type="button" data-id="' + result['data'].id + '" class="btn btn-delete-saas btn-danger ms-2">Delete</button>';
        if (result['data'].active && result['data'].active == 0) {
          temp += '<button type="button" class="btn btn-acive-saas btn-success ms-2" data-id="' + result['data'].id + '" id="active_deactive">Activate</button>';
        } else {
          temp += '<button type="button" class="btn btn-deacive-saas btn-danger ms-2" data-id="' + result['data'].id + '" id="active_deactive">Deactivate</button>';
        }

        $("#loffy-btn").html(temp);
        var startingDate = moment(result['data'].current_plan_expiry, 'DD MMM YYYY').toDate();

        $('#end_date').daterangepicker({
          locale: {
            format: date_format_js
          },
          singleDatePicker: true,
          startDate: startingDate,
        });

        $("#saas-edit-modal").trigger("click");

      } else {
        iziToast.error({
          title: something_wrong_try_again,
          message: "",
          position: 'topRight'
        });
      }
    },
    complete: function () {
      $(".loader-progress").remove();
    }
  });
});
$(document).on('click', '.btn-delete-saas', function (e) {
  e.preventDefault();
  var id = $(this).data("id");
  console.log(id);
  Swal.fire({
    title: 'Are you sure?',
    text: you_want_to_delete_this_user_all_related_data_with_this_user_also_will_be_deleted,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'OK'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: base_url + 'auth/delete_user',
        data: "id=" + id,
        dataType: "json",
        success: function (result) {
          if (result['error'] == false) {
            location.reload();
          } else {
            modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>').find('.alert').delay(4000).fadeOut();
          }
        }
      });
    }
  });
});

$(document).on('click', '.btn-acive-saas', function (e) {
  e.preventDefault();
  var id = $(this).data("id");
  Swal.fire({
    title: 'Are you sure?',
    text: you_want_to_activate_this_user,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'OK'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: base_url + 'auth/activate',
        data: "id=" + id,
        dataType: "json",
        success: function (result) {
          if (result['error'] == false) {
            location.reload();
          } else {
            modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>').find('.alert').delay(4000).fadeOut();
          }
        }
      });
    }
  });
});
$(document).on('click', '.btn-deacive-saas', function (e) {
  e.preventDefault();
  var id = $(this).data("id");
  console.log(id);
  Swal.fire({
    title: 'Are you sure?',
    text: you_want_to_deactivate_this_user_this_user_will_be_not_able_to_login_after_deactivation,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'OK'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: base_url + 'auth/deactivate',
        data: "id=" + id,
        dataType: "json",
        success: function (result) {
          if (result['error'] == false) {
            location.reload();
          } else {
            modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>').find('.alert').delay(4000).fadeOut();
          }
        }
      });
    }
  });
});

$(document).on('click', '.btn-login-saas', function (e) {
  e.preventDefault();
  var id = $(this).data("id");
  console.log(id);
  Swal.fire({
    title: are_you_sure,
    text: you_will_be_logged_out_from_the_current_account,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'OK'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: base_url+'auth/login_as_admin', 
        data: "id="+id,
        dataType: "json",
        success: function (result) {
          if (result['error'] == false) {
            location.reload();
          } else {
            modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>').find('.alert').delay(4000).fadeOut();
          }
        }
      });
    }
  });
});
