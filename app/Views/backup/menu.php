<head>
    <!-- jQuery Smoothness CSS -->
    <link href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
    <!-- treeSortable CSS -->
    <link href="/css/treeSortable.css" rel="stylesheet" type="text/css" />
    <!-- sortable CSS -->
    <link href="/css/sortable.css" rel="stylesheet" type="text/css" />
</head>

<button id="add-menu-btn" type="button" class="btn btn-primary col-lg-1 col-2">Add</button>

<div id="menu-list-container" class="list-group col gy-1 mt-2">
    <ul id="tree"></ul>
</div>

<script type="text/javascript">
$(document).ready(function() {
    const treeId = "#tree"
    const data = [{
            id: 1,
            parent_id: 0,
            title: "Branch 1",
            level: 1,
        },
        {
            id: 2,
            parent_id: 1,
            title: "Branch 2",
            level: 2,
        },
        {
            id: 3,
            parent_id: 1,
            title: "Branch 3",
            level: 2,
        },
        {
            id: 4,
            parent_id: 2,
            title: "Branch 4",
            level: 3,
        }
    ];

    const tree = new TreeSortable({
        treeSelector: treeId,
        maxLevel: 10,
        depth: 5,
        // dataAttributes: {
        //     id: "id",
        //     parent: "parent",
        //     title:"title",
        //     level: "level"
        // }
    });

    initTreeData(data);

    $("#add-menu-btn").click(function() {
        var d = $(treeId).sortable("toArray", {
            attribute: "id"
        });
        console.log(d);
        // initTreeData(d);
    });

    function initTreeData(treeData) {
        tree.onSortCompleted((event, ui) => {
            console.log($(treeId).sortable("toArray", {
                attribute: "id"
            }));
            // here the `event` is the sortable event.
            // The `ui` is the jquery-ui's ui object.
            // You can use the `ui.item`, `ui.helper` and so on.
            // See https://api.jqueryui.com/sortable/
        });

        tree.addListener('click', '.remove-branch', function(event, instance) {
            event.preventDefault();

            const confirm = window.confirm('Are you sure you want to delete this branch?');
            if (!confirm) {
                return;
            }
            instance.removeBranch($(event.target));
        });

        const $content = treeData.map(tree.createBranch);
        $(treeId).html($content);
        tree.run();
    }
});
</script>


<div class="row d-none">
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

<!-- jQuery UI Library -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<!-- treeSortable library -->
<script src="/js/treeSortable.js"></script>
<script src="/js/tooltip.min.js"></script>