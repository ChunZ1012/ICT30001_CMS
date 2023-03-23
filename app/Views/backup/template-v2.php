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
    <link rel="stylesheet" href="/css/styles.css"/>
    <!-- SweetAlert 2 CSS -->
    <link href="/css/sweetalert2/sweetalert2.min.css" rel="stylesheet" />
    <!-- SweetAlert 2 JS -->
    <script src="/js/sweetalert2/sweetalert2.min.js"></script>
    <script src="/js/custom.js"></script>
</head>

<body>
    <div class="container-fluid overflow-hidden">
        <div class="row vh-100 overflow-auto">
            <div class="col-12 col-sm-3 col-xl-2 px-sm-2 px-0 bg-dark d-flex sticky-top">
                <div class="d-flex flex-sm-column flex-row flex-grow-1 align-items-center align-items-sm-start px-3 pt-2 text-white">
                    <a href="<?= base_url('home'); ?>"
                        class="d-flex align-items-center pb-sm-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-5"><?= $brand; ?></span>
                    </a>
                    <ul class="nav nav-pills flex-sm-column flex-row flex-nowrap flex-shrink-1 flex-sm-grow-0 flex-grow-1 mb-sm-auto mb-0 justify-content-center align-items-center align-items-sm-start"
                        id="menu">
                        <li class="nav-item">
                            <a href="<?= base_url('home'); ?>" class="nav-link px-sm-0 px-2">
                                <i class="fs-5 bi-house"></i><span class="ms-1 d-none d-sm-inline">Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('menu'); ?>" class="nav-link px-sm-0 px-2">
                                <i class="fs-5 bi-speedometer2"></i><span class="ms-1 d-none d-sm-inline">Menu</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('content/list'); ?>" class="nav-link px-sm-0 px-2">
                                <i class="fs-5 bi-table"></i><span class="ms-1 d-none d-sm-inline">Content</span></a>
                        </li>
                        <li>
                            <a href="<?= base_url('publish/list'); ?>" class="nav-link px-sm-0 px-2">
                                <i class="fs-5 bi-table"></i><span class="ms-1 d-none d-sm-inline">Publication</span></a>
                        </li>
                    </ul>
                    <div class="dropdown py-sm-4 mt-sm-auto ms-auto ms-sm-0 flex-shrink-1">
                        <a id="logout-btn" class="d-flex align-items-center text-white text-decoration-none flex-grow-1 align-middle"><i
                                class="fa-solid fa-right-from-bracket"></i><span
                                class="ms-1 d-none d-md-inline-block">Logout</span></a>
                    </div>
                </div>
            </div>
            <div class="col d-flex flex-column h-100">
                <main class="row overflow-auto p-2">
                    <div class="page-header pt-2 pb-0">
                        <h2 class="fw-bold"> <?= $title; ?></h2>
                    </div>
                    <?= (is_null($description) || empty($description) ? "" : '<p class="lead">'.$description.'</p>'); ?>
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
        $(function(){
            $("#logout-btn").click(function(e){
                e.preventDefault();

                $.post({
                    url:'<?= base_url('api/logout') ?>',
                    headers: {
                        'Authorization': 'Bearer ' + $.cookie('<?= session()->get('token_access_key') ?>')
                    },
                    success:(r) => {
                        window.location.href = '<?= base_url('login'); ?>'
                    },
                    error: (r) => {
                        alert(r.responseText.msg);
                        window.location.href = '<?= base_url('login'); ?>'
                    }
                })
            });
        });
    </script>
</body>
</html>