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

    <style>
    #sidebar-nav {
        width: 200px;
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto px-0 position-sticky">
                <div id="sidebar" class="collapse collapse-horizontal show border-end p-2">
                    <ul id="sidebar-nav"
                        class="nav nav-pills list-group d-flex flex-column align-items-stretch border-0 rounded-0 text-sm-start min-vh-100 gy-1"
                        role="tablist" aria-orientation="vertical">
                        <a href=<?= base_url('home'); ?>
                            class="nav-link <?= $_SERVER['REQUEST_URI'] == '/home' ? 'active' : '' ; ?> d-inline-block text-truncate text-center"
                            type="button" role="tab" data-bs-parent="#sidebar" aria-current="home"
                            aria-selected="true"><span>Menu
                                Management</span> </a>
                        <a href=<?= base_url('content'); ?>
                            class="nav-link <?= $_SERVER['REQUEST_URI'] == '/content' ? 'active' : '' ; ?> d-inline-block text-truncate text-center"
                            type="button" data-bs-parent="#sidebar" aria-current="content"><span>Content
                                Management</span> </a>
                    </ul>
                </div>
            </div>
            <main class="col ps-md-2 pt-2">
                <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse"
                    class="border rounded-3 p-1 text-decoration-none"><i class="bi bi-list bi-lg py-2 p-1"></i> Menu</a>
                <div class="page-header pt-3">
                    <h2><?= $title; ?></h2>
                </div>
                <?= (is_null($description) || empty($description) ? "" : '<p class="lead">'.$description.'</p>'); ?>
                <hr>
                <?= $content; ?>
            </main>
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
    <!-- Sortable -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script> -->
    <!-- Sortable jQuery Wrapper -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script> -->
</body>

</html>