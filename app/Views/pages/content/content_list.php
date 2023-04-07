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
        <a type="button" class="btn btn-success col me-1" id="add-btn" href="<?= base_url('content/add'); ?>">Add</a>
        <a type="button" class="btn btn-primary col me-1" id="refresh-btn" onclick="javascript:void(0);">Refresh</a>
    </div>
    <div id="grid"></div>

    <!-- <div class="row align-items-center align-middle"><span class="col-1"><i class="fa-solid fa-pen-to-square" onclick="save("></i></span></div> -->
</div>

<script type="text/javascript">
const grid = new gridjs.Grid({
    columns: [{
            id: 'page-cb',
            name: 'Select',
            plugin: {
                component: gridjs.plugins.selection.RowSelection,
            }
        },
        {
            id: 'page-id',
            name: '#',
            hidden: true
        },
        {
            id: 'page-title',
            name: "Title"
        },
        {
            id: 'page-published-time',
            name: "Published Time"
        },
        {
            id: 'page-is-active',
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
                    onClick: () => editContent(row.cells[1].data)
                })), gridjs.h('span', {
                    className: 'col-1'
                }, gridjs.h('i', {
                    className: 'fa-solid fa-trash',
                    onClick: () => deleteContent(row.cells[1].data)
                })), gridjs.h('span', {
                    className: 'col-1'
                }, gridjs.h('i', {
                    className: 'fa-sharp fa-solid fa-' + (row.cells[4].data == '1' ?
                        'ban' : 'check'),
                    onClick: () => setContentStatus(row.cells[1].data, row.cells[4]
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
}

function createDataReqObj() {
    return {
        url: '<?= base_url('api/content/list'); ?>',
        method: 'GET',
        headers:{
            'Authorization': $.cookie('token')
        },
        then: r => {
            if(r.error) throw new Error("An error happened while fetching the data");
            else {
                return r.data.map(c => [c.id, c.title, c.published_time, c.is_active, null])
            }
        },
        handle: (r) => {
            if (r.status == 200) return r.json();
            else return {
                data: []
            }
        },
    }
}

function editContent(id) {
    window.location.href = '<?= base_url('content/edit/'); ?>' + id;
}

function deleteContent(id) {
    var c = window.confirm('Are you sure to delete this content?');
    if (c) {
        $(function() {
            $.ajax({
                method:'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + $.cookie('<?= session()->get('token_access_key') ?>')
                },
                url: '<?= base_url('api/content/delete/'); ?>' + id,
                success:(r) => {
                    if(!r.error) toastSuccess('Successfully deleted!');
                    else {
                        toastError('Error when deleting the content!');
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
                }
            })
        });
    }
}

function setContentStatus(id, cur_status) {
    var c = cur_status == 1 ? 'deactivate' : 'activate';
    var confirm = window.confirm('Are you sure to ' + c + ' this content?');

    if (confirm) {
        $.ajax({
            method:'PUT',
            url: '<?= base_url('api/content/'); ?>' + c + '/' + id,
            headers: {
                'Authorization': 'Bearer ' + $.cookie('<?= session()->get('token_access_key') ?>')
            },
            success:(r) => {
                if(!r.error) toastSuccess('Successfully updated!');
                else {
                    toastError('Error when updating the content!');
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