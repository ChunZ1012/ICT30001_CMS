<?php
    $filePrefix = base_url();
    $is_edit = isset($id) && $id > 0;
    if($is_edit)
    {
        $catModel = new \App\Models\PublicationCategory();
        $cate = $catModel->select(
            'id, shortcode, name, is_active'
        )->find($id);

        if(is_null($cate)) 
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
<?= form_open_multipart(base_url('api/publish/category/').(isset($cate) ? 'edit/'.$cate['id'] : 'add'), [
    'class' => 'd-flex flex-column',
    'id' => 'cate-form'
]); ?>
    <!-- Action buttons -->
    <div class="d-flex flex-row mb-1 ms-auto">
        <a href="<?= base_url('publish/list'); ?>" type="button" class="btn btn-danger ms-1 me-1">Cancel</a>
        <input type="submit" class="btn btn-success mx-1" value="Save"/>
    </div>
    <?= form_hidden('pub-id', isset($cate) ? $cate['id'] : ''); ?>
    <!-- Publication title -->
    <div class="mb-3">
        <?= form_label("Short Code", "", [
            'class' => 'form-label',
            'for' => 'pc-sc'
        ]); ?>
        <?= form_input("pc-sc", isset($cate) ? $cate['shortcode'] : '', [
            'class' => 'form-control',
            'id' => 'pc-sc',
            'required' => ''
        ]); ?>
    </div>
    <!-- Publication publish time -->
    <div class="mb-3">
        <?= form_label("Category Name", "", [
            'class' => 'form-label',
            'for' => 'pc-name'
        ]); ?>
        <?= form_input("pc-name", isset($cate) ? $cate['name'] : '', [
            'class' => 'form-control',
            'id' => 'pc-name',
            'required' => ''
        ]); ?>
    </div>
    <div class="mb-3">
        <?= form_label("Is Active", "", [
            'class' => 'form-label',
            'for' => 'pc-is-active'
        ]); ?>
        <?= form_dropdown(
            "pc-is-active", 
            [
                1 => 'Active',
                0 => 'Deactivate'
            ],
            isset($cate) ? $cate['is_active'] : '', 
            [
                'class' => 'form-select',
                'id' => 'pub-is-active',
                'required' => ''
            ]);
        ?>
    </div>
</form>

<script type="text/javascript">
    $(function(){
        $("#cate-form").submit(function(e){
            e.preventDefault();

            var data = {
                'pc-sc' : $("#pc-sc"),
                'pc-name' : $("#pc-name"),
                'pc-is-active' : $("#pc-is-active"),
            };

            $.post({
                url:'<?= base_url('api/publish/category/').(isset($cate) ? 'edit/'.$cate['id'] : 'add'); ?>',
                headers: {
                    'Authorization' : 'Bearer ' + $.cookie('<?= session()->get('token_access_key') ?>')
                },
                dataType:'json',
                data:JSON.stringify(data),
                contentType:'application/json',
                success: (r) => {
                    if(!r.error) {
                        toastSuccess('Successfully added!')
                    }
                    else {
                        toastError('Error when saving the publication category information!')
                        toastError(r.msg)
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
        });
    });
</script>