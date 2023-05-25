<head>
    <!-- SunEditor -->
    <link href="/css/suneditor/suneditor.min.css" rel="stylesheet" />

    <script src="/js/suneditor/suneditor.min.js"></script>
    <script src="/js/content.js"></script>
</head>


<?php
$is_edit = isset($id) && $id > 0;
if ($is_edit) {
    $postModel = new \App\Models\Post();
    $postImageModel = new \App\Models\PostImage();
    $post = $postModel->select(
        'id, title, date_format(published_time, "%Y-%m-%d") as published_time, is_active, content'
    )->find($id);

    if (is_null($post)) 
    {
?>
<!-- Stop render and going back to list -->
<script type="text/javascript">
    alert('The selected publication is no longer exist!');
    window.location.href = '<?=base_url('publish/list');?>';
</script>
<?php
    }
    else
    {
        $postImage = $postImageModel->getImages($post['id']);
        $post['images'] = $postImage;
    }
}
?>

<div class="row">
    <form class="d-flex flex-column" id="page-form" method="post" enctype="multipart/form-data">
        <!-- Action buttons -->
        <div class="d-flex flex-row mb-1 ms-auto">
            <a href="<?=base_url('content/list');?>" type="submit" class="btn btn-danger ms-2">Cancel</a>
            <button type="submit" class="btn btn-success ms-2" id="submit-btn" onclick="javascript:void(0)" value="Save">Save</button>
        </div>
        <!-- Page title -->
        <div class="mb-3">
            <?=form_label('Page Title', '', [
    'class' => 'form-label',
    'for' => 'page-title',
]);?>
            <?=form_input('page-title', isset($post) ? $post['title'] : '', [
    'class' => 'form-control',
    'id' => 'page-title',
    'required' => '',
], 'text');?>
            <div class="invalid-feedback"></div>
        </div>
        <!-- Page publish time -->
        <div class="mb-3">
            <?=form_label('Page Publish Time', '', [
    'class' => 'form-label',
    'for' => 'page-publish-time',
]);?>
                <?=form_input('page-publish-time', isset($post) ? $post['published_time'] : '', [
    'class' => 'form-control',
    'id' => 'page-publish-time',
    'required' => '',
], 'date');?>
            <div class="invalid-feedback"></div>
        </div>
<?php
    if(get_user_role(session()) == 1)
    {
        echo view('templates/select_dropdown', [
            'id' => 'page-is-active',
            'select_options' => [
                1 => 'Publish',
                0 => 'Unpublish'
            ],
            'label' => 'Is Published',
            'active' => (isset($post) ? $post['is_active'] : 0),
            'required' => true,
            'comment' => '<!-- Page Is Active -->'
        ]);
    }
?>
        </div>
        <!-- Page cover page pictures -->
        <div class="mb-3">
            <label for="c-image" class="form-label">Cover Image</label>
            <button type="button" class="btn btn-outline-primary" id="btn-add-image" onclick="$('#page-cover').click();">Add Image</button>
            <input type="file" class="d-none" id="page-cover" class="form-control" accept="image/*" multiple/>
            <div class="mt-3 preview-images-zone" id="preview-images-zone"></div>
        </div>
        <!-- Page Content -->
        <div class="mb-3">
            <label for="wysiwyg-editor" class="form-label">Content</label>
            <textarea id="wysiwyg-editor" class="form-control"></textarea>
        </div>
    </form>
</div>

<div class="modal fade" id="modalImageContent" tabindex="-1" aria-labelledby="modalImageContentLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImageContentLabel">Image Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-3">
                    <label for="page-image-alt-text" class="col-form-label">Image Alt Text</label>
                    <input type="text" class="form-control" name="page-image-alt-text" id="page-image-alt-text"/>
                </div>
                <div class="mt-3">
                    <label for="page-image-content" class="col-form-label">Image Content</label>
                    <textarea type="text" class="form-control" name="page-image-content" id="page-image-content"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="page-image-save-btn">Save</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
<?php
if (isset($post)) {
    foreach ($post['images'] as $src) {
        if (isset($src['path']) && !empty($src['path'])) {
            ?>
addPreviewImage('<?=base_url($src['path'])?>');
saveToDict(num - 1, '<?=base_url($src['path']);?>', '<?= $src['description']; ?>', '<?= $src['content']; ?>');
<?php
}
    }
}
?>
$(function() {
    $(document).keydown(function(e){
        if(e.ctrlKey && e.keyCode == 83) {
            e.preventDefault();
            $("#page-form").submit();
        }
    });
    // Content editor
    editor = SUNEDITOR.create((document.getElementById('wysiwyg-editor')), editorConfig);
    editor.setDefaultStyle('font-family:Arial;font-size:12px');
    // Set content to the editor
    editor.setContents('<?=isset($post) ? $post['content'] : '';?>');

    modalEditor = SUNEDITOR.create((document.getElementById('page-image-content')), editorConfig);
    modalEditor.setDefaultStyle('font-family:Arial;font-size:12px');

    // Submit listener
    $("form#page-form").submit(function(e) {
        e.preventDefault();
        $(this).removeClass('was-validated');

        $fd = new FormData();
        $fd.append("page-title", $("#page-title").val());
        $fd.append("page-is-active", $("#page-is-active").val());
        $fd.append("page-publish-time", $("#page-publish-time").val());
        $fd.append("page-content", editor.getContents(true));
        $fd.append("page-cover-count", imgDict.length);
        $.map(imgDict, function(f, idx){
            var imgMeta = {
               'page-image-alt-text': f.altText,
               'page-image-content': f.desc
            }; 

            $fd.append("page-cover-" + idx, f.file);
            $fd.append("page-cover-meta-" + idx, JSON.stringify(imgMeta));
        });

        $.post({
            url: '<?=isset($post) ? base_url('api/content/edit/' . $post['id']) : base_url('api/content/add');?>',
            headers: {
                'Authorization': 'Bearer ' + $.cookie('<?=session()->get('token_access_key')?>')
            },
            dataType: 'json',
            data: $fd,
            contentType:false,
            processData:false,
            success:(r) => {
                if(!r.error) {
                    $('#submit-btn').prop('disabled', true);
                    toastSuccess('Successfully saved!', true);
                }
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
                        $("form#page-form")[0].checkValidity();
                        $("form#page-form").addClass('was-validated');
                    }
                    else toastError($r.msg);
                }
            },
        });
    });
});
</script>