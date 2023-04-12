<?php
$is_edit = isset($id) && $id > 0;
if ($is_edit) {
    $staffModel = new \App\Models\Staff();
    $post = $staffModel->select(
        'id, name, contact, email, age, gender, office_contact, office_fax'
    )->find($id);

    if (is_null($post)) {
        ?>
<!-- Stop render and going back to list -->
<script type="text/javascript">
    alert('The selected staff is no longer exist!');
    window.location.href = '<?=base_url('staff/list');?>';
</script>
<?php
}
}
?>

<div class="row">
    <form class="d-flex flex-column" id="staff-form" method="post" novalidate>
        <!-- Action buttons -->
        <div class="d-flex flex-row mb-1 ms-auto">
            <a href="<?=base_url('staff/list');?>" type="submit" class="btn btn-danger ms-2">Cancel</a>
            <button type="submit" class="btn btn-success ms-2">Save</button>
        </div>
        <!-- Staff Name -->
        <div class="mb-3">
            <?=form_label('Staff Name', '', [
                'class' => 'form-label',
                'for' => 'staff-name',
            ]);?>
            <?= form_input('staff-name', isset($post) ? $post['name'] : '', [
                'class' => 'form-control',
                'id' => 'staff-name',
                'required' => '',
            ], 'text');?>
            <div class="invalid-feedback"></div>
        </div>
        <!-- Staff Age -->
        <div class="mb-3">
            <?=form_label('Staff Age', '', [
                'class' => 'form-label',
                'for' => 'staff-age',
            ]);?>
            <?=form_input('staff-age', isset($post) ? $post['age'] : '', [
                'class' => 'form-control',
                'id' => 'staff-age',
                'pattern' => '[0-9]{2,3}',
                'required' => '',
            ], 'number');?>
            <div class="invalid-feedback"></div>
        </div>
        <!-- Staff Gender -->
        <div class="mb-3">
            <?=form_label("Staff Gender", "", [
                'class' => 'form-label',
                'for' => 'staff-gender',
            ]);?>
            <?=form_dropdown("staff-gender", [
                'F' => 'Female',
                'M' => 'Male',
            ], isset($post) ? $post['gender'] : '',[
                'class' => 'form-select',
                'id' => 'staff-gender',
                'required' => '',
            ]); ?>
            <div class="invalid-feedback"></div>
        </div>
        <!-- Staff Contact -->
        <div class="mb-3">
            <?=form_label('Staff Contact', '', [
                'class' => 'form-label',
                'for' => 'staff-contact',
            ]);?>
            <?=form_input('staff-contact', isset($post) ? $post['contact'] : '', [
                'class' => 'form-control',
                'id' => 'staff-contact',
                'pattern' => '[0-9]{3}-[0-9]{7,8}',
                'required' => '',
            ], 'text');?>
            <div class="invalid-feedback"></div>
        </div>
        <!-- Staff Email -->
        <div class="mb-3">
            <?=form_label('Staff Email', '', [
                'class' => 'form-label',
                'for' => 'staff-email',
            ]);?>
            <?=form_input('staff-email', isset($post) ? $post['email'] : '', [
                'class' => 'form-control',
                'id' => 'staff-email',
                'required' => '',
            ], 'email');?>
            <div class="invalid-feedback"></div>
        </div>
        <!-- Staff Office Contact -->
        <div class="mb-3">
            <?=form_label('Staff Office Contact', '', [
                'class' => 'form-label',
                'for' => 'staff-office-contact',
            ]);?>
            <?=form_input('staff-office-contact', isset($post) ? $post['office_contact'] : '', [
                'class' => 'form-control',
                'id' => 'staff-office-contact',
                'pattern' => '[0-9]{3}-[0-9]{6}',
                'max-length' => 10,
                'required' => ''
            ], 'text');?>
            <div class="invalid-feedback"></div>
        </div>
        <!-- Staff Fax -->
        <div class="mb-3">
            <?=form_label('Staff Office Fax', '', [
                'class' => 'form-label',
                'for' => 'staff-office-fax',
            ]);?>
            <?=form_input('staff-office-fax', isset($post) ? $post['office_fax'] : '', [
                'class' => 'form-control',
                'id' => 'staff-office-fax',
                'pattern' => '[0-9]{3}-[0-9]{6}',
                'max-length' => 10,
                'required' => ''
            ], 'text');?>
            <div class="invalid-feedback"></div>
        </div>
    </form>
</div>

<script type="text/javascript">
$(function() {
    $(document).keydown(function(e){
        if(e.ctrlKey && e.keyCode == 83) {
            e.preventDefault();
            $("#staff-form").submit();
        }
    });

    // Submit listener
    $("form#staff-form").submit(function(e) {
        e.preventDefault();
        $(this).removeClass('was-validated');

        $data = {
            'staff-name': $("#staff-name").val(),
            'staff-age': $("#staff-age").val(),
            'staff-gender': $("#staff-gender").val(),
            'staff-contact': $("#staff-contact").val(),
            'staff-email': $("#staff-email").val(),
            'staff-office-contact': $("#staff-office-contact").val(),
            'staff-office-fax': $("#staff-office-fax").val(),
        }
    
        $.ajax({
            method:'<?= isset($post) ? 'put' : 'post' ?>',
            url: '<?=isset($post) ? base_url('api/staff/edit/' . $post['id']) : base_url('api/staff/add');?>',
            headers: {
                'Authorization': 'Bearer ' + $.cookie('<?=session()->get('token_access_key')?>')
            },
            dataType: 'json',
            contentType:'application/json',
            data: JSON.stringify($data),
            success:(r) => {
                if(!r.error) toastSuccess('Successfully saved!');
                else {
                    toastError('Error when saving the content!');
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
                        $("form#staff-form")[0].checkValidity();
                        $("form#staff-form").addClass('was-validated');
                    }
                    else toastError($r.msg);
                }
            },
        });
    });
});
</script>