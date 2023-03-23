<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
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

    <style>
    #sidebar-nav {
        width: 160px;
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto px-0">
                <div id="sidebar" class="collapse collapse-horizontal show border-end">
                    <div id="sidebar-nav" class="list-group border-0 rounded-0 text-sm-start min-vh-100">
                        <a href="#" class="list-group-item border-end-0 d-inline-block text-truncate"
                            data-bs-parent="#sidebar"><i class="bi bi-bootstrap"></i> <span>Item</span> </a>
                        <a href="#" class="list-group-item border-end-0 d-inline-block text-truncate"
                            data-bs-parent="#sidebar"><i class="bi bi-film"></i> <span>Item</span></a>
                    </div>
                </div>
            </div>
            <main class="col ps-md-2 pt-2">
                <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse"
                    class="border rounded-3 p-1 text-decoration-none"><i class="bi bi-list bi-lg py-2 p-1"></i> Menu</a>
                <div class="page-header pt-3">
                    <h2>Bootstrap 5 Sidebar Menu - Simple</h2>
                </div>
                <p class="lead">A offcanvas "push" vertical nav menu example.</p>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <p>This is a simple collapsing sidebar menu for Bootstrap 5. Unlike the Offcanvas component that
                            overlays the content, this sidebar will "push" the content. Sriracha biodiesel taxidermy
                            organic post-ironic, Intelligentsia salvia mustache 90's code editing brunch. Butcher
                            polaroid VHS art party, hashtag Brooklyn deep v PBR narwhal sustainable mixtape swag wolf
                            squid tote bag. Tote bag cronut semiotics, raw denim deep v taxidermy messenger bag. Tofu
                            YOLO Etsy, direct trade ethical Odd Future jean shorts paleo. Forage Shoreditch tousled
                            aesthetic irony, street art organic Bushwick artisan cliche semiotics ugh synth chillwave
                            meditation. Shabby chic lomo plaid vinyl chambray Vice. Vice sustainable cardigan,
                            Williamsburg master cleanse hella DIY 90's blog.</p>
                        <p>Ethical Kickstarter PBR asymmetrical lo-fi. Dreamcatcher street art Carles, stumptown
                            gluten-free Kickstarter artisan Wes Anderson wolf pug. Godard sustainable you probably
                            haven't heard of them, vegan farm-to-table Williamsburg slow-carb readymade disrupt deep v.
                            Meggings seitan Wes Anderson semiotics, cliche American Apparel whatever. Helvetica cray
                            plaid, vegan brunch Banksy leggings +1 direct trade. Wayfarers codeply PBR selfies. Banh mi
                            McSweeney's Shoreditch selfies, forage fingerstache food truck occupy YOLO Pitchfork fixie
                            iPhone fanny pack art party Portland.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?= $page; ?>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>
    <!-- jQuery Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <!-- Sortable -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>

    <script>

    </script>
</body>

</html>