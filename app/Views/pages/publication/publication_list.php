<head>
    <!-- Grid.js -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/gridjs/dist/gridjs.umd.js"></script> -->
    <link href="/css/gridjs/mermaid.min.css" rel="stylesheet" />
    <script src="/js/gridjs/gridjs.umd.js"></script>
    <script src="/js/gridjs/selection.umd.js"></script>
    <!-- Grid js Selection Plugin -->
    <!-- <script src="https://unpkg.com/gridjs/plugins/selection/dist/selection.umd.js"></script>     -->
</head>

<div class="d-flex flex-column">
    <div class="row ms-auto mb-2">
        <a type="button" class="btn btn-success col me-1" id="add-btn" href="<?= base_url('publish/add'); ?>">Add</a>
        <a type="button" class="btn btn-primary col me-1" id="refresh-btn" onclick="javascript:void(0);">Refresh</a>
    </div>
    <div id="grid"></div>
</div>

<script type="text/javascript">
const grid = new gridjs.Grid({
    columns: [
        {
            id: 'pub-cb',
            name: 'Select',
            plugin: {
                component: gridjs.plugins.selection.RowSelection,
            },
            width:"5%"
        },
        {
            id: 'pub-id',
            name: '#',
            hidden: true
        },
        {
            id: 'pub-title',
            name: "Title"
        },
        {
            id: 'pub-published-time',
            name: "Published Time"
        },
        {
            id: 'pub-created-by',
            name: "Created By"
        },
        {
            id: 'pub-created-at',
            name: "Created At"
        },
        {
            id: 'pub-is-active',
            name: "Is Active",
            formatter: (cell, row) => {
                return cell == 1 ? 'Active' : 'Deactivated'
            },
            hidden: false,
        },
        {
            id: 'action-cell',
            name: "Action",
            formatter: (cell, row) => {
                return gridjs.h('div', {
                    className: 'row align-items-center align-middle'
                }, gridjs.h('span', {
                    className: 'col-1'
                }, gridjs.h('i', {
                    className: 'fa-solid fa-pen-to-square',
                    id: 'edit-btn',
                    onClick: () => editPublication(row.cells[1].data)
                })), gridjs.h('span', {
                    className: 'col-1'
                }, gridjs.h('i', {
                    className: 'fa-solid fa-trash',
                    onClick: () => deletePublication(row.cells[1].data)
                })), gridjs.h('span', {
                    className: 'col-1'
                }, gridjs.h('i', {
                    className: 'fa-sharp fa-solid fa-' + (row.cells[4].data == '1' ?
                        'ban' : 'check'),
                    onClick: () => setPublicationStatus(row.cells[1].data, row.cells[4]
                        .data)
                })));
            }
        }
    ],
    search: true,
    autoWidth: true,
    sort: true,
    resizable: true,
    pagination: {
        limit: 10,
        page: 0,
        summary: true,
        nextButton: true,
        prevButton: true,
        buttonsCount: true,
        resetPageOnUpdate: false
    },
    server: createDataReqObj()
}).render(document.getElementById("grid"));

grid.config.store.subscribe(function(state) {
    var slctCount = state.rowSelection?.rowIds?.length;
    if (slctCount > 0) console.log(state.rowSelection?.rowIds);
})

function updateGrid() {
    grid.updateConfig({
        server: createDataReqObj()
    }).forceRender();
    console.clear()
}

function createDataReqObj() {
    return {
        url: '<?= base_url('api/publish/list'); ?>',
        method: 'GET',
        then: r => r.data.data.map(c => [c.id, c.title, c.published_time, c.created_by, c.created_at, c.is_active, null]),
        handle: (r) => {
            if (r.status == 200) return r.json();
            else return {
                data: []
            }
        },
    }
}

function editPublication(id) {
    window.location.href = '<?= base_url('publish/edit/'); ?>' + id;
}

function deletePublication(id) {
    var c = window.confirm('Are you sure to delete this content?');
    if (c) {
        $(function() {
            $.ajax({
                method:'DELETE',
                url: '<?= base_url('api/publish/delete/'); ?>' + id,
                headers: {
                    'Authorization': 'Bearer ' + $.cookie('<?= session()->get('token_access_key') ?>')
                },
                success:(r) => {
                    if(!r.error) toastSuccess('Successfully deleted!');
                    else {
                        toastError('Error when deleting the publication!');
                        toastError(r.msg);
                    }
                    updateGrid();
                },
                error:(e) => {
                    if(e.status == 401) toastError('Please login before continue');
                    else {
                        $r = $.parseJSON(e.responseText);
                        toastError($r.msg);
                    }
                },
            })
        });
    }
}

function setPublicationStatus(id, cur_status) {
    var c = cur_status == 1 ? 'deactivate' : 'activate';
    var confirm = window.confirm('Are you sure to ' + c + ' this publication?');

    if (confirm) {
        $.ajax({
            method:'PUT',
            url: '<?= base_url('api/publish/'); ?>' + c + '/' + id,
            headers: {
                'Authorization': 'Bearer ' + $.cookie('<?= session()->get('token_access_key') ?>')
            },
            success:(r) => {
                if(!r.error) toastSuccess('Successfully updated!');
                else {
                    toastError('Error when updating the publication!');
                    toastError(r.msg);
                }
                updateGrid();
            },
            error:(e) => {
                if(e.status == 401) toastError('Please login before continue');
                else {
                    $r = $.parseJSON(e.responseText);
                    toastError($r.msg);
                }
            },
        })
    }
}

$(function() {
    $("#refresh-btn").click(function(e) {
        e.preventDefault();
        updateGrid();
    });
});
</script>