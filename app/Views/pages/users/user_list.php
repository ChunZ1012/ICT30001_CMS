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
        <a type="button" class="btn btn-success col me-1" id="add-btn" href="<?=base_url('user/add');?>">Add</a>
        <a type="button" class="btn btn-primary col me-1" id="refresh-btn" onclick="javascript:void(0);">Refresh</a>
    </div>
    <div id="grid"></div>

    <!-- <div class="row align-items-center align-middle"><span class="col-1"><i class="fa-solid fa-pen-to-square" onclick="save("></i></span></div> -->

    <div class="modal fade" id="modalSetPassword" tabindex="-1" aria-labelledby="modalSetPasswordLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSetPasswordLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="password-form" method="post" href="#">
                        <div class="mt-3">
                            <label for="neww-password" class="col-form-label">New Password</label>
                            <input type="password" class="form-control"     name="new-password" id="new-password"/>
                        </div>
                        <div class="mt-3">
                            <label for="confirm-password" class="col-form-label">Confirm Password</label>
                            <input type="password" class="form-control"     name="confirm-password" id="confirm-password"/>
                            <div class="invalid-feedback"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="password-submit-btn">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
const grid = new gridjs.Grid({
    columns: [{
            id: 'user-cb',
            name: 'Select',
            plugin: {
                component: gridjs.plugins.selection.RowSelection,
            }
        },
        {
            id: 'user-id',
            name: '#',
            hidden: true
        },
        {
            id: 'user-display-name',
            name: "Name"
        },
        {
            id: 'user-email',
            name: "Email"
        },
        {
            id: 'user-role',
            name: "Role",
            formatter: (cell, row) => {
                return cell == 1 ? 'Administrators' : 'Users'
            },
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
                    className:'fa-solid fa-key',
                    id:'reset-btn',
                    onClick: () => showSetPasswordModal(row.cells[1].data)
                })), gridjs.h('span', {
                    className: 'col-1'
                }, gridjs.h('i', {
                    className: 'fa-solid fa-trash',
                    onClick: () => deleteContent(row.cells[1].data)
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
        url: '<?=base_url('api/user/list');?>',
        method: 'GET',
        headers:{
            'Authorization': $.cookie('token')
        },
        then: r => {
            if(r.error) throw new Error("An error happened while fetching the data");
            else {
                return r.data.map(c => [c.id, c.display_name, c.email, c.role, null])
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
    window.location.href = '<?=base_url('user/edit/');?>' + id;
}

function deleteContent(id) {
    var c = window.confirm('Are you sure to delete this staff?');
    if (c) {
        $(function() {
            $.ajax({
                method:'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + $.cookie('<?=session()->get('token_access_key')?>')
                },
                url: '<?=base_url('api/user/delete/');?>' + id,
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

function showSetPasswordModal(id) {
    var htmlModal = document.getElementById('modalSetPassword');
    var bsModal = new bootstrap.Modal(htmlModal);

    htmlModal.setAttribute('data-bs-id', id);
    bsModal.toggle();
}

$(function() {
    $("#refresh-btn").click(function(e) {
        e.preventDefault();
        updateGrid();
    });

    $("#password-submit-btn").click(function(e){
        $("#password-form").trigger('submit');
    });

    $("#password-form").validate({
        rules: {
            "new-password": {
                minlength:8
            },
            'confirm-password': {
                minlength: 8,
                equalTo:'#new-password'
            }
        },
        submitHandler: function(form) {
            var htmlModal = document.getElementById("modalSetPassword");

            $.ajax({
                method:'put',
                url: '<?= base_url('user/reset-password/'); ?>' + htmlModal.getAttribute("data-bs-id"),
                contentType:'application/json',
                dataType:'json',
                data:'',
                success: (r) => {
                    if(!r.error) {
                        toastSuccess('Successfully reseted!');
                    }
                    else {
                        toastError('Error when resetting the password!');
                        toastError(r.msg);
                    }
                },
                error:(e) => {
                    if(e.status == 401) toastError('Please login before continue');
                    else {
                        $r = $.parseJSON(e.responseText);
                        if($r.validate_error) {
                            $m = $.parseJSON($r.msg);
                            $.each($m, function(k, v){
                                toastError(v);
                            });
                        }
                        else toastError($r.msg);
                    }
                }
            })
        }
    });
});
</script>