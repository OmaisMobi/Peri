"use strict";

var amount = 0;
var plan_id = '';
var card_progress = '';

$(document).on('click', '.payment-button', function () {

  var card = $(this).closest('.card');
  card_progress = $.cardProgress(card, {
    spinner: true
  });

  amount = $(this).data("amount");
  plan_id = $(this).data("id");
  saas_id = $(this).data("saas");
  console.log(saas_id, plan_id, amount);

  $("#plan_id").val(plan_id);

  // Free Renew
  if (amount == 0) {
    $.ajax({
      type: "POST",
      url: base_url + 'plans/order-completed/',
      data: "amount=" + amount + "&plan_id=" + plan_id + "&saas_id=" + saas_id,
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
    return false;
  }
  if ($('#payment-div').hasClass('d-none')) {
    $('#payment-div').removeClass('d-none');
  }


  // Paystack
  $(document).on('click', '#paystack-button', function (e) {
    if (paystack_public_key != "") {
      $('#paystack-button').addClass('disabled');
      var handler = PaystackPop.setup({
        key: paystack_public_key,
        email: paystack_user_email_id,
        amount: amount * 100,
        currency: currency_code,
        callback: function (response) {
          $('#paystack-button').removeClass('disabled');
          if (response.status == 'success') {
            $.ajax({
              type: "POST",
              url: base_url + 'plans/order-completed/',
              data: "amount=" + amount + "&status=1&plan_id=" + plan_id,
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
          } else {
            iziToast.error({
              title: something_wrong_try_again,
              message: "",
              position: 'topRight'
            });
          }
        },
        onClose: function () {
          $('#paystack-button').removeClass('disabled');
          iziToast.error({
            title: something_wrong_try_again,
            message: "",
            position: 'topRight'
          });
        },
      });
      handler.openIframe();
    }
  });

  // Paypal
  if (paypal_client_id != "") {

    $('#paypal-button').empty();

    paypal.Buttons({
      onClick: function (data, actions) {
        return fetch(base_url + 'plans/validate/' + plan_id, {
          method: 'post',
          headers: {
            'content-type': 'application/json'
          }
        }).then(function (res) {
          return res.json();
        }).then(function (data) {
          if (data.validationError) {
            iziToast.error({
              title: something_wrong_try_again,
              message: "",
              position: 'topRight'
            });
            return actions.reject();
          } else {
            if (plan_id == data.plan[0]['id'] && amount == data.plan[0]['price']) {
              return actions.resolve();
            } else {
              iziToast.error({
                title: something_wrong_try_again,
                message: "",
                position: 'topRight'
              });
              return actions.reject();
            }
          }
        });
      },

      createOrder: function (data, actions) {
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: amount
            }
          }]
        });
      },

      onApprove: function (data, actions) {
        return actions.order.capture().then(function (details) {
          var status = 0;
          if (details.status == "COMPLETED") {
            status = 1;
          }
          $.ajax({
            type: "POST",
            url: base_url + 'plans/order-completed/',
            data: "amount=" + amount + "&status=" + status + "&plan_id=" + plan_id,
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
        });
      }
    }).render('#paypal-button').then(function () {

    });
  }

  $('html, body').animate({
    scrollTop: $("#paypal-button").offset().top
  }, 1000);
  card_progress.dismiss(function () {
  });

});

// Stripe
if (get_stripe_publishable_key != "") {
  var stripe = Stripe(get_stripe_publishable_key);
  var stripeButton = document.getElementById('stripe-button');
  stripeButton.addEventListener('click', function () {
    $('#stripe-button').addClass('disabled');

    fetch(base_url + 'plans/create-session/' + plan_id + '/' + saas_id, {
      method: 'POST',
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (session) {
        if (session.error != true) {
          console.log(session);
          return stripe.redirectToCheckout({ sessionId: session.id });
        }
      })
      .then(function (result) {
        $('#stripe-button').removeClass('disabled');
        iziToast.error({
          title: something_wrong_try_again,
          message: "",
          position: 'topRight'
        });
      })
      .catch(function (error) {
        $('#stripe-button').removeClass('disabled');
        iziToast.error({
          title: something_wrong_try_again,
          message: "",
          position: 'topRight'
        });
      });
  });
}

// MyFatoorah
if (get_myfatoorah_secret_key != "") {
  var fatoorahButton = document.getElementById('fatoorah-button');
  fatoorahButton.addEventListener('click', function () {
    $('#fatoorah-button').addClass('disabled');
    window.location = base_url + 'payment/create-session/'+plan_id;
  });
}
// Razorpay
$(document).on('click', '#razorpay-button', function (e) {
  $('#razorpay-button').addClass('disabled');
  if (razorpay_key_id != "") {
    var options = {
      "key": razorpay_key_id,
      "amount": amount * 100,
      "currency": currency_code,
      "name": company_name,
      "description": "Subscription",
      "handler": function (response) {
        if (response.razorpay_payment_id) {
          $.ajax({
            type: "POST",
            url: base_url + 'plans/order-completed/',
            data: "amount=" + amount + "&status=1&plan_id=" + plan_id,
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
              $('#razorpay-button').removeClass('disabled');
            }
          });
        } else {
          $('#razorpay-button').removeClass('disabled');
          iziToast.error({
            title: something_wrong_try_again,
            message: "",
            position: 'topRight'
          });
        }
      }
    };
    var rzp1 = new Razorpay(options);
    rzp1.on('payment.failed', function (response) {
      $('#razorpay-button').removeClass('disabled');
      iziToast.error({
        title: something_wrong_try_again,
        message: "",
        position: 'topRight'
      });
    });
    rzp1.open();
    e.preventDefault();
  } else {
    $('#razorpay-button').removeClass('disabled');
    iziToast.error({
      title: something_wrong_try_again,
      message: "",
      position: 'topRight'
    });
  }
});

// Offline / Bank Transfer

$("#bank-transfer-form").submit(function (e) {
  e.preventDefault();

  let save_button = $(this).find('.savebtn'),
    output_status = $(this).find('.result');

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
        output_status.find('.alert').delay(4000).fadeOut();
      }
      save_button.removeClass('btn-progress');
    },
    error: function () {
      iziToast.error({
        title: something_wrong_try_again,
        message: "",
        position: 'topRight'
      });
      save_button.removeClass('btn-progress');
    }
  });
});

// $(document).on('click','#offline-button',function(){
//   $('#offline-button').addClass('btn-progress');
//   if(offline_bank_transfer != ""){
//   swal({
// 		title: wait,
// 		text: we_will_contact_you_for_further_process_of_payment_as_soon_as_possible_click_ok_to_confirm,
// 		icon: 'warning',
// 		buttons: true,
// 		dangerMode: true,
// 		}).then((willDelete) => {
//       if (willDelete) {
//         $.ajax({
//           type: "POST",
//           url: base_url+'plans/create-offline-request/',
//           data: "plan_id="+plan_id,
//           dataType: "json",
//           success: function(result)
//           {
//             if(result['error'] == false){
//                 location.reload();
//             }else{
//               iziToast.error({
//                 title: result['message'],
//                 message: "",
//                 position: 'topRight'
//               });
//             }
//             $('#offline-button').removeClass('btn-progress');
//           }
//         });
//       }
//       $('#offline-button').removeClass('btn-progress');
//     });
//   }else{
//     $('#razorpay-button').removeClass('btn-progress');
//     iziToast.error({
//       title: something_wrong_try_again,
//       message: "",
//       position: 'topRight'
//     });
//   }
// });
