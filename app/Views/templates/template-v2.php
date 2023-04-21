<!doctype html>
<html lang="en">

<head>
    <title><?= $title; ?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- jQuery Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="/css/styles.css" rel="stylesheet"/>
    <!-- SweetAlert 2 CSS -->
    <link href="/css/sweetalert2/sweetalert2.min.css" rel="stylesheet" />
    <!-- SweetAlert 2 JS -->
    <script src="/js/sweetalert2/sweetalert2.min.js"></script>
    <script src="/js/custom.js"></script>
    <!-- jQuery Cookie -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js"></script>
</head>

<body>
    <div class="container-fluid overflow-hidden">
        <div class="row vh-100 overflow-auto">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark col-lg-2">
                <div class="container-fluid d-lg-flex flex-lg-column mb-lg-auto align-items-lg-start">
                    <a class="navbar-brand" href="#"><?= $brand; ?></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-content" aria-controls="navbar-content" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbar-content">
                        <ul class="navbar-nav me-auto mb-1 mb-lg-0 d-lg-flex flex-lg-column">
                            <li class="nav-item">
                                <a class="nav-link active" href='<?= base_url('/') ?>'>Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('content/list') ?>">Content</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('publish/list') ?>">Publication</a>
                            </li>
                            <?php if(get_user_role(session()) == '1')
                            {
                                echo '<li class="nav-item">
                                <a class="nav-link" href="'.base_url('staff/list').'">Staff</a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link" href="'.base_url('user/list').'">Users</a>
                                </li>';
                            }
                            ?>
                            <li class="nav-item">
                                <a id="logout-btn" href="#" class="nav-link"><i
                                class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="col-lg d-flex flex-column h-100">
                <main class="row overflow-auto p-2">
                    <div class="page-header pt-2 pb-0">
                        <h2 class="fw-bold"> <?= $title; ?></h2>
                    </div>
                    <?= (isset($description) || is_null($description) || empty($description) ? "" : '<p class="lead">'.$description.'</p>'); ?>
                    <hr>
                    <?= $content; ?>
                </main>
                <footer class="row bg-light py-4 mt-auto">
                    <div class="col"> Footer content here... </div>
                </footer>
            </div>
        </div>
    </div>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>
    <!-- jQuery validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    </script>

    <script type="text/javascript">
    $(function() {
        $("#logout-btn").click(function(e) {
            e.preventDefault();
            $key = '<?= session()->get('token_access_key'); ?>';
            $.post({
                url: '<?= base_url('api/auth/logout') ?>',
                headers: {
                    'Authorization': 'Bearer ' + $.cookie('<?= session()->get('token_access_key') ?>')
                },
                success: (r) => {
                    if(!r.error) toastSuccess('Successfully logged out!');
                    else toastError('Error when logging out!');
                },
                error: (e) => {
                    $r = $.parseJSON(e.responseText);
                    toastError($r.msg);
                },
                complete:() => {
                    $.removeCookie($key);
                    setTimeout(() => {
                        window.location.href = '<?= base_url('login'); ?>'
                    }, 1000);
                }
            })
        });
    });
    </script>
</body>

</html>