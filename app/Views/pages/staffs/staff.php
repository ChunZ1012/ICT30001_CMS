<?php
$filePrefix = base_url();
$is_edit = isset($id) && $id > 0;
if ($is_edit) {
    $staffModel = new \App\Models\Staff();
    $post = $staffModel->select(
        'id, name, contact, CONCAT(\''.getenv("PUBLIC_UPLOAD_PATH").'avatars/\', avatar) as avatar, email, age, gender, office_contact, office_fax, IFNULL(location, \'\') as location, IFNULL(position, \'\') as position'
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
    <form class="d-flex flex-column" id="staff-form" name="staff-form" method="post" enctype="multipart/form-data" novalidate>
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
                'name' => 'staff-name',
                'required' => '',
            ], 'text');?>
            <label class="invalid-feedback"></label>
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
                'name' => 'staff-age',
                'pattern' => '[0-9]{2,3}',
                'required' => '',
            ], 'number');?>
            <label class="invalid-feedback"></label>
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
            <label class="invalid-feedback"></label>
        </div>
        <!-- Staff Avatar -->
        <div class="mb-3">
            <label for="staff-avatar" class="form-label">Choose Image</label>
            <div class="input-group">
                <input type="file" class="form-control" id="staff-avatar" name="staff-avatar" accept="image/*"
                    aria-describedby="staff-avatar-help-text" <?= isset($pub) ? '' : 'required' ?>>
                    <?= isset($post) && $is_edit ? view('templates/preview_btn', ['link' => $filePrefix.$post['avatar']]) : ''; ?>
                </input>
                <label class="invalid-feedback"></label>
            </div>
            <div id="staff-avatar-help-text" class="form-text">Use this to upload the image of staff</div>
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
            <label class="invalid-feedback"></label>
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
            <label class="invalid-feedback"></label>
        </div>
        <!-- Staff Position -->
        <div class="mb-3">
            <?=form_label('Staff Position', '', [
                'class' => 'form-label',
                'for' => 'staff-position',
            ]);?>
            <?=form_input('staff-position', isset($post) ? $post['position'] : '', [
                'class' => 'form-control',
                'id' => 'staff-position',
                'required' => ''
            ], 'text');?>
            <label class="invalid-feedback"></label>
        </div>
         <!-- Staff Location -->
         <div class="mb-3">
            <?=form_label('Staff Location', '', [
                'class' => 'form-label',
                'for' => 'staff-location',
            ]);?>
            <?=form_input('staff-location', isset($post) ? $post['location'] : '', [
                'class' => 'form-control',
                'id' => 'staff-location',
                'required' => ''
            ], 'text');?>
            <label class="invalid-feedback"></label>
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
            <label class="invalid-feedback"></label>
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
            <label class="invalid-feedback"></label>
        </div>
    </form>
</div>

<script type="text/javascript">
$(function() {
    $(document).keydown(function(e) {
        if (e.ctrlKey && e.keyCode == 83) {
            e.preventDefault();
            $("#staff-form").submit();
        }
    });

    // Submit listener
    $("form#staff-form").submit(function(e) {
        e.preventDefault();
        $(this).removeClass('was-validated');

        $fd = new FormData($(this)[0]);
        for (var pair of $fd.entries()) {
            console.log(pair[0]+ ', ' + pair[1]);
        }
        toastLoading();

        $.post({
            url: '<?=isset($post) ? base_url('api/staff/edit/' . $post['id']) : base_url('api/staff/add');?>',
            headers: {
                'Authorization': 'Bearer ' + $.cookie('<?=session()->get('token_access_key')?>')
            },
            dataType: 'json',
            processData:false,
            contentType:false,
            data: $fd,
            success: (r) => {
                if (!r.error) toastSuccess('Successfully saved!');
                else {
                    toastError('Error when saving the content!');
                    toastError(r.msg);
                }
            },
            error: (e) => {
                if (e.status == 401) toastError('Please login before continue');
                else {
                    $r = $.parseJSON(e.responseText);
                    if ($r.validate_error) {
                        $m = $.parseJSON($r.msg);
                        $.each($m, function(k, v) {
                            toastError(v);
                            $("#" + k + " ~ label.invalid-feedback").html(v);
                            $("#" + k).parent().siblings(".invalid-feedback").html(v);
                        });
                        $("form#staff-form")[0].checkValidity();
                        $("form#staff-form").addClass('was-validated');
                    } else toastError($r.msg);
                }
            },
        });
    });
});
</script>