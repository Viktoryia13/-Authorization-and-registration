$(function() {
    $(".signUp").hide();
    $("#signup-box-link").click(function() {
        $(".signIn").fadeOut(100);
        $(".signUp").delay(100).fadeIn(100);
        $("#login-box-link").removeClass("active");
        $("#signup-box-link").addClass("active");
    });
    $("#login-box-link").click(function() {
        $(".signIn").delay(100).fadeIn(100);;
        $(".signUp").fadeOut(100);
        $("#login-box-link").addClass("active");
        $("#signup-box-link").removeClass("active");
    });

    // обработка кнопки авторизации
    $('#signIn').click(function() {
        $.ajax({
            type: 'POST',
            url: '/users/signIn',
            dataType: 'json',
            data: $('.signIn').serialize(),
            success: function(data) {
                console.log(3)
                if (data.success) {
                    //перезагрузка страницы
                    location.reload();
                } else {
                    // Вывод ошибок при регистрации
                    if (data.login) {
                        $('#login-errors').html(data.login).show();
                    } else {
                        $('#login-errors').empty()
                    }
                    if (data.password) {
                        $('#password-errors').html(data.password).show();
                    } else {
                        $('#password-errors').empty()
                    }
                }

            }
        });
    });

    // обработка кнопки регистрации
    $('#signUp').click(function() {
        $.ajax({
            type: 'POST',
            url: '/users/signUp',
            dataType: 'json',
            data: $('.signUp').serialize(),
            success: function(data) {
                if (data.success) {
                    // Успешная регистрация.
                    $('.signUp-inputs').hide();
                    $('.sign-up-success').html(data.message).show()
                } else {
                    // Вывод ошибок при регистрации
                    if (data.login) {
                        $('#sign-up-login-errors').html(data.login).show();
                    } else {
                        $('#sign-up-login-errors').empty()
                    }
                    if (data.password) {
                        $('#sign-up-password-errors').html(data.password).show();
                    } else {
                        $('#sign-up-password-errors').empty()
                    }
                    if (data.confirm_password) {
                        $('#sign-up-confirm-password-errors').html(data.confirm_password).show();
                    } else {
                        $('#sign-up-confirm-password-errors').empty()
                    }
                    if (data.email) {
                        $('#sign-up-email-errors').html(data.email).show();
                    } else {
                        $('#sign-up-email-errors').empty()
                    }
                    if (data.name) {
                        $('#sign-up-name-errors').html(data.name).show();
                    } else {
                        $('#sign-up-name-errors').empty()
                    }

                }
            }
        });
    });
})