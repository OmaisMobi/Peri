$(document).ready(function () {
    $(".password-toggle, .password-toggle2").on("click", function () {
        var toggleButton = $(this);
        var passwordInput = $(this).prev('input[type="password"]');
        if (passwordInput.length === 0) {
            var passwordInput = $(this).prev('input[type="text"]');
        }

        if (passwordInput.attr("type") === "password") {
            passwordInput.attr("type", "text");
            toggleButton.removeClass("fa-eye").addClass("fa-eye-slash");
        } else {
            passwordInput.attr("type", "password");
            toggleButton.removeClass("fa-eye-slash").addClass("fa-eye");
        }
    });

    $("#exampleModal").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        // Make separate AJAX requests for login and signup content
        var loginRequest = $.ajax({
            url: "<?= asset('auth') ?>",
            type: "GET",
        });

        var signupRequest = $.ajax({
            url: "<?= asset('auth/register') ?>",
            type: "GET",
        });

        // Handle successful responses using Promise.all
        $.when(loginRequest, signupRequest)
            .done(function (loginResponse, signupResponse) {
                modal.find(".login .modal-body-login").html(loginResponse[0]);
                modal
                    .find(".signup .modal-body-signup")
                    .html(signupResponse[0]);
                console.log("Login and signup forms loaded successfully!");
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Error loading forms:", textStatus, errorThrown);
                // Handle errors (e.g., display error message)
            });
    });

    const loginRadio = $("#login");
    const signupRadio = $("#signup");
    const loginContent = $(".modal-body-login");
    const signupContent = $(".modal-body-signup");
    const sliderTab = $(".slider-tab");

    function showLogin() {
        console.log("shoing login");
        loginContent.parent().show();
        loginContent.show();
        signupContent.parent().hide();
        sliderTab.css("left", "0");
        // loginContent.parent().css('height', "100px");
    }

    function showSignup() {
        loginContent.parent().hide();
        signupContent.parent().show();
        signupContent.show();
        // loginContent.parent().css('height', "0px");
        sliderTab.css("left", "50%");
    }

    loginRadio.change(showLogin);
    signupRadio.change(showSignup);

    $(".x8oe2yx").click(function () {
        signupRadio.click();
    });

    $(".x8oe2yx2").click(function () {
        loginRadio.click();
    });

    // Initial display setup
    showLogin();
});

////// modal based css ends here
////// modal based css ends here
////// modal based css ends here
////// modal based css ends here
