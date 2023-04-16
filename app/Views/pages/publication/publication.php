<?php
    $filePrefix = base_url();
    $is_edit = isset($id) && $id > 0;
    if($is_edit)
    {
        $pubModel = new \App\Models\Publication();
        $pub = $pubModel->select(
            'id, title, is_active, date_format(published_time, "%Y-%m-%d") as published_time, CONCAT(\''.getenv("PUBLIC_UPLOAD_PATH").'\', cover) as cover, CONCAT(\''.getenv("PUBLIC_UPLOAD_PATH").'\', pdf) as pdf'
        )->find($id);

        if(is_null($pub)) 
        {
?>
<!-- Stop render and going back to list -->
<script type="text/javascript">
    alert('The selected publication is no longer exist!');
    window.location.href = '<?= base_url('publish/list'); ?>';
</script>
<?php
        }
    }
?>

<script type="text/javascript">
<?php 
    if(isset($validate_error) && $validate_error)
    {
?>
        alertify.set('notifier', 'position', 'bottom-right');
<?php foreach($errors as $e): ?>
        alertify.error("<?= $e; ?>", 3);
<?php endforeach; ?>
<?php
    }
    else if(isset($error))
    {
        if($error)
        {
?>
        alertify.error('<?= (isset($msg) ? $msg : ''); ?>', 3);
<?php
        }
        else
        {
?>
        alertify.success('<?= (isset($msg) ? $msg : ''); ?>', 3);
<?php
        }
    }
    
?>
</script>


<!-- <form id="pub-form" class="d-flex flex-column" enctype="multipart/form-data" method="POST"> -->
<?= form_open_multipart(base_url('api/publish/').(isset($pub) ? 'edit/'.$pub['id'] : 'add'), [
    'class' => 'd-flex flex-column',
    'id' => 'pub-form',
    'novalidate' => ''
]); ?>
    <!-- Action buttons -->
    <div class="d-flex flex-row mb-1 ms-auto">
        <a href="<?= base_url('publish/list'); ?>" type="button" class="btn btn-danger ms-1 me-1">Cancel</a>
        <input type="submit" class="btn btn-success mx-1" value="Save"/>
    </div>
    <?= form_hidden('pub-id', isset($pub) ? $pub['id'] : ''); ?>
    <!-- Publication title -->
    <div class="mb-3">
        <?= form_label("Publication Title", "", [
            'class' => 'form-label',
            'for' => 'pub-title'
        ]); ?>
        <?= form_input("pub-title", isset($pub) ? $pub['title'] : '', [
            'class' => 'form-control',
            'id' => 'pub-title',
            'required' => ''
        ]); ?>
        <div class="invalid-feedback"></div>
    </div>
    <!-- Publication publish time -->
    <div class="mb-3">
        <?= form_label("Publication Time", "", [
            'class' => 'form-label',
            'for' => 'pub-publish-time'
        ]); ?>
        <?= form_input("pub-publish-time", isset($pub) ? $pub['published_time'] : '', [
            'class' => 'form-control',
            'id' => 'pub-publish-time',
            'required' => ''
        ], "date"); ?>
        <div class="invalid-feedback"></div>
    </div>
    <div class="mb-3">
        <?= form_label("Is Active", "", [
            'class' => 'form-label',
            'for' => 'pub-is-active'
        ]); ?>
        <?= form_dropdown(
            "pub-is-active", 
            [
                1 => 'Active',
                0 => 'Deactivate'
            ],
            isset($pub) ? $pub['is_active'] : '', 
            [
                'class' => 'form-select',
                'id' => 'pub-is-active',
                'required' => ''
            ]);
        ?>
        <div class="invalid-feedback"></div>
    </div>
    <!-- Publication Cover -->
    <div class="mb-3">
        <label for="pub-cover" class="form-label">Choose Cover</label>
        <div class="d-flex flex-row">
            <input type="file" class="form-control" id="pub-cover" name="pub-cover" accept="image/*" aria-describedby="pub-cover-help-text" <?= isset($pub) ? '' : 'required' ?>>
            <?= isset($pub) ? view('templates/preview_btn', ['link' => $filePrefix.$pub['cover']]) : ''; ?>
        </div>
        <div id="pub-cover-help-text" class="form-text">Use this to upload the cover of publication</div>
        <div class="invalid-feedback"></div>
    </div>
    <!-- Publication Content -->
    <div class="mb-3">
        <label for="pub-file" class="form-label">Choose file</label>
        <div class="d-flex flex-row">
            <input type="file" class="form-control" id="pub-file" name="pub-file" accept="application/pdf" aria-describedby="pub-file-help-text" <?= isset($pub) ? '' : 'required' ?>>
            <?= isset($pub) ? view('templates/preview_btn', ['link' => $filePrefix.$pub['pdf']]) : ''; ?>
        </div>
        <div id="pub-file-help-text" class="form-text">Use this to upload the pdf of the publication</div>
        <div class="invalid-feedback"></div>
    </div>
</form>

<script type="text/javascript">
    $(function(){
        $("#pub-form").submit(function(e){
            e.preventDefault();
            $(this).removeClass('was-validated');

            $form = $(this)[0];
            $fd = new FormData($form);

            toastLoading();

            $.post({
                url:'<?= base_url('api/publish/').(isset($pub) ? 'edit/'.$pub['id'] : 'add'); ?>',
                headers: {
                    'Authorization' : 'Bearer ' + $.cookie('<?= session()->get('token_access_key') ?>')
                },
                dataType:'json',
                data:$fd,
                processData:false,
                contentType:false,
                success: (r) => {
                    Swal.close()
                    if(!r.error) {
                        toastSuccess('Successfully added!');
                    }
                    else {
                        toastError('Error when saving the publication information!');
                        toastError(r.msg);
                    }
                },
                error:(e) => {
                    Swal.close()
                    if(e.status == 401) toastError('Please login before continue');
                    else {
                        $r = $.parseJSON(e.responseText);
                        if($r.validate_error) {
                            $m = $.parseJSON($r.msg);
                            $.each($m, function(k, v){
                                toastError(v);
                                $("#"+k+" ~ div.invalid-feedback").html(v);
                            });
                            $("form#pub-form")[0].checkValidity();
                            $("form#pub-form").addClass('was-validated');
                        }
                        else toastError($r.msg);
                    }
                }
            })
        });
    });
</script>