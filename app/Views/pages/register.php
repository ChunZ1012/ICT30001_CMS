<?php
    $user = new \App\Models\User();

    $num_of_users = $user->selectCount('id')->first()['id'];

    if($num_of_users > 0)
    {
?>
    <script type="text/javascript">
        window.location.href = '<?= base_url('login');?>';
    </script>
<?php 
    } 
?>

<!doctype html>
<html lang="en">
<head>
    <title>Register</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- SweetAlert 2 CSS -->
    <link href="/css/sweetalert2/sweetalert2.min.css" rel="stylesheet" />
    <link href="/css/styles.css" rel="stylesheet"/>
</head>

<body class="container-fluid">
    <main class="d-flex flex-column align-items-center h-100 p-4">
        <h1 class="fw-bold">Register</h1>
        <form id="register-form" method="POST" accept="utf-8" class="col-lg-8 col-10">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required/>
                <div class="invalid-feedback">123</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Your Password" spellcheck="false" autocorrect="false" autocapitalize="false" autocomplete="password" required/>
                    <span type="button" class="input-group-text" id="toggle-password-visibility">
                        <i class="fa-regular fa-eye" id="password-icon"></i>
                    </span>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="mb-3">
                <label for="confirm-password" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirm-password" name="confirm-password" placeholder="Confirm Password" spellcheck="false" autocorrect="false" autocapitalize="false" autocomplete="password" required/>
                    <span type="button" class="input-group-text" id="toggle-password-visibility">
                        <i class="fa-regular fa-eye" id="password-icon"></i>
                    </span>    
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="mb-3">
                <label for="display-name" class="form-label">Display Name</label>
                <input type="text" class="form-control" id="display-name" name="display-name" placeholder="Your Display Name" required/>
                <div class="invalid-feedback"></div>
            </div>
            <button type="submit" class="btn btn-primary float-end">Register</button>
        </form>
    </main>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>
    <!-- jQuery Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <!-- jQuery validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <!-- jQuery Cookie -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js"></script>
    <!-- SweetAlert 2 JS -->
    <script src="/js/sweetalert2/sweetalert2.min.js"></script>
    <script src="js/custom.js"></script>

    <script type="text/javascript">
        $(function(){
            $("#toggle-password-visibility").click(function(e){
                e.preventDefault();
                
                $pass = $("#password");
                $passIcon = $("#password-icon");
                $type = $pass.attr("type");

                if($type == "password"){
                    $pass.attr("type", "text");
                    $passIcon.removeClass('fa-eye');
                    $passIcon.addClass('fa-eye-slash');
                }
                else {
                    $pass.attr("type", "password");
                    $passIcon.removeClass('fa-eye-slash');
                    $passIcon.addClass('fa-eye');
                }
            });

            $("#register-form").validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true
                    },
                    'confirm-password': {
                        required: true,
                        equalTo:"#password"
                    }
                },
                submitHandler: function(f){
                    $(this).removeClass('was-validated');
                    var data = {
                        "email" : $("#email").val(),
                        "password" : $("#password").val(),
                        "confirm-password": $("#confirm-password").val(),
                        "display-name" : $("#display-name").val(),
                    };
                    $.post({
                        url: '<?= base_url('api/auth/register'); ?>',
                        contentType:'application/json',
                        data: JSON.stringify(data),
                        dataType:'json',
                        success:function(r){
                            toastSuccess('Successfully Registered!');
                            setTimeout(() => {
                                window.location.href = '<?= base_url('login'); ?>'
                            }, 1000);
                        },
                        error:function(r){
                            $r = $.parseJSON(r.responseText);
                            if($r.validate_error) {
                                $m = $.parseJSON($r.msg);
                                $.each($m, function(k, v){
                                    toastError(v);
                                    
                                    $t = $("#"+k+" ~ div.invalid-feedback");
                                    $t.html(v);
                                    $t.addClass("d-block");
                                });
                            }
                            else toastError($r.msg);
                        }
                    })
                }
            });
        });
    </script>
</body>
</html>