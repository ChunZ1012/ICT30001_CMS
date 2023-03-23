<head>
    <!-- jQuery Smoothness CSS -->
    <link href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
    <!-- treeSortable CSS -->
    <link href="/css/treeSortable/treeSortable.css" rel="stylesheet" type="text/css" />
    <!-- sortable CSS -->
    <link href="/css/treeSortable/sortable.css" rel="stylesheet" type="text/css" />
</head>

<div class="d-flex flex-row flex-nowrap">
    <button id="add-menu-btn" type="button" class="btn btn-primary col-lg-1 col-2 me-3">Add</button>
    <button id="save-menu-btn" type="button" class="btn btn-success col-lg-1 col-2">Save</button>
</div>

<div id="menu-list-container" class="list-group col gy-1 mt-2">
    <ul id="tree"></ul>
</div>

<script type="text/javascript">
$(document).ready(function() {
    const treeId = "#tree"
    var data = [{
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

    initTreeData(data);
    $("#add-menu-btn").click(function() {
        newData = getAvailableTreeNodes();
        newData.push({
            id: newData[newData.length - 1].id + 1,
            parent_id: 0,
            title: "Lorem Ipsum",
            level: 1,
        });
        data = newData
        initTreeData(data);
    });

    $("#save-menu-btn").click(function() {
        menuData = getAvailableTreeNodes();
        $.ajax({
            url: '<?= base_url('api/menu/update'); ?>',
            method: "POST",
            data: JSON.stringify(menuData),
            dataType: 'application/json',
            headers: {
                Authorization: 'Bearer '
            }
        }).success(function(r) {
            console.log('Success: ' + r);
        }).fail(function(r) {
            console.log('Failed: ' + r);
        });
    });

    function getAvailableTreeNodes() {
        var newData = [];
        $(treeId + " > li.tree-branch").each(function() {
            var self = $(this);
            var e = $.grep(data, function(val) {
                return val.id == self.attr('data-id');
            });
            newData.push(e[0]);
        });

        return newData;
    }

    function initTreeData(treeData) {
        var tree = new TreeSortable({
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

        tree.addListener('click', '.remove-branch', function(event, instance) {
            event.preventDefault();

            // const confirm = window.confirm('Are you sure you want to delete this branch?');
            // if (!confirm) {
            //     return;
            // }
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
<script src="/js/treeSortable/treeSortable.js"></script>
<script src="/js/treeSortable/tooltip.min.js"></script>