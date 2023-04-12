<?php
$is_edit = isset($id) && $id > 0;
if ($is_edit) {
    $userModal = new \App\Models\User();
    $user = $userModal->select(
        'id, email, display_name, role'
    )->find($id);

    if (is_null($user)) {
        ?>
<!-- Stop render and going back to list -->
<script type="text/javascript">
    alert('The selected staff is no longer exist!');
    window.location.href = '<?=base_url('user/list');?>';
</script>
<?php
}
}
?>

<div class="row">
    <form class="d-flex flex-column" id="user-form" method="post" novalidate>
        <!-- Action buttons -->
        <div class="d-flex flex-row mb-1 ms-auto">
            <a href="<?=base_url('user/list');?>" type="submit" class="btn btn-danger ms-2">Cancel</a>
            <button type="submit" class="btn btn-success ms-2">Save</button>
        </div>
        <!-- User Display Name -->
        <div class="mb-3">
            <?=form_label('User Display Name', '', [
                'class' => 'form-label',
                'for' => 'user-display-name',
            ]);?>
            <?= form_input('user-display-name', isset($user) ? $user['display_name'] : '', [
                'class' => 'form-control',
                'id' => 'user-display-name',
                'autocomplete' => 'off',
                'required' => '',
            ], 'text');?>
            <div class="invalid-feedback"></div>
        </div>
        <!-- User Email -->
        <div class="mb-3">
            <?=form_label('User Email', '', [
                'class' => 'form-label',
                'for' => 'user-email',
            ]);?>
            <?=form_input('user-email', isset($user) ? $user['email'] : '', [
                'class' => 'form-control',
                'id' => 'user-email',
                'autocomplete' => 'off',
                'required' => '',
            ], 'email');?>
            <div class="invalid-feedback"></div>
        </div>
        <!-- User Role -->
        <div class="mb-3">
            <?=form_label("User Role", "", [
                'class' => 'form-label',
                'for' => 'user-role',
            ]);?>
            <?=form_dropdown("user-role", [
                '1' => 'Administrators',
                '2' => 'Users',
            ], isset($user) ? $user['role'] : '',[
                'class' => 'form-select',
                'id' => 'user-role',
                'required' => '',
            ]); ?>
            <div class="invalid-feedback"></div>
        </div>
        <!-- User Password -->
        <div class="mb-3">
            <?=form_label('User Password', '', [
                'class' => 'form-label',
                'for' => 'user-password',
            ]);?>
            <?=form_input('user-password', '', [
                'class' => 'form-control',
                'id' => 'user-password',
                'autocomplete' => 'off',
                'minlength' => 8,
                !isset($user) ? 'required' : '' => ''
            ], 'password');?>
            <div class="invalid-feedback"></div>
        </div>
    </form>
</div>

<script type="text/javascript">
$(function() {
    $(document).keydown(function(e){
        if(e.ctrlKey && e.keyCode == 83) {
            e.preventDefault();
            $("#user-form").submit();
        }
    });

    // Submit listener
    $("form#user-form").submit(function(e) {
        e.preventDefault();
        $(this).removeClass('was-validated');

        $data = {
            'user-display-name': $("#user-display-name").val(),
            'user-email': $("#user-email").val(),
            'user-password': $("#user-password").val(),
            'user-role': $("#user-role").val()
        };
    
        $.ajax({
            method:'<?= isset($user) ? 'put' : 'post' ?>',
            url: '<?=isset($user) ? base_url('api/user/edit/' . $user['id']) : base_url('api/user/add');?>',
            headers: {
                'Authorization': 'Bearer ' + $.cookie('<?=session()->get('token_access_key')?>')
            },
            dataType: 'json',
            contentType:'application/json',
            data: JSON.stringify($data),
            success:(r) => {
                if(!r.error) toastSuccess('Successfully saved!');
                else {
                    toastError('Error when saving the user information!');
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
                            $("#"+k+" ~ div.invalid-feedback").html(v);
                        });
                        $("form#user-form")[0].checkValidity();
                        $("form#user-form").addClass('was-validated');
                    }
                    else toastError($r.msg);
                }
            },
        });
    });
});
</script>