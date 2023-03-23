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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        #sidebar{
            width:280px;
            position:fixed;
            top:0;
            left:0;
            height:100vh!important;
            z-index:999;
            transition:all 0.3s;
        }
        #sidebar-nav > li > a{
            height:40px;
        }
        #sidebar.active{
            margin-left:-280px;
        }
    </style>
</head>

<body class="container-fluid d-flex">
    <nav id="sidebar" class="d-flex flex-column flex-shrink p-3 bg-light">
        <ul id="sidebar-nav" class="list-unstyled nav nav-pills flex-column mb-auto">
            <li class="nav-item align-items-center">
                <a href="#" type="button" class="nav-link active rounded align-self-center">
                    <i class="fas fa-align-left"></i>
                    Menu Management
                </a>
            </li>
            <li class="nav-item align-items-center">
                <a href="#" type="button" class="nav-link text-decoration-none rounded align-self-center">
                    <i class="fas fa-align-left"></i>
                    Menu Management
                </a>
            </li>
        </ul> 
    </nav>
    <div id="content" class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <button id="sidebar-collapse" type="button" class="btn" style="background:lightgrey">
                    <i class="fas fa-align-left"></i>
                </button>
            </div>
        </nav>
    </div>

    <footer>
        <!-- place footer here -->
    </footer>
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
        $(function(){
            $("#sidebar-collapse").click(function(){
                $("#sidebar").toggleClass('active');
            });
        });
    </script>
</body>

</html>