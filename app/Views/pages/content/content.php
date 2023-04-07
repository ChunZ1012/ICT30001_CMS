<head>
    <!-- SunEditor -->
    <link href="/css/suneditor/suneditor.min.css" rel="stylesheet" />
    <link href="/css/jQuery.filer/jquery.filer.css" rel="stylesheet" />

    <script src="/js/suneditor/suneditor.min.js"></script>
    <script src="/js/content.js"></script>
    <script src="/js/jQuery.filer/jquery.filer.min.js"></script>
</head>


<?php
    $is_edit = isset($id) && $id > 0;
    if($is_edit)
    {
        $postModel = new \App\Models\Post();
        $post = $postModel->select(
            'id, title, date_format(published_time, "%Y-%m-%d") as published_time, is_active, content, cover'
        )->find($id);

        if(is_null($post)) 
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

<div class="row">
    <form class="d-flex flex-column" id="page-form" method="post" enctype="multipart/form-data">
        <!-- Action buttons -->
        <div class="d-flex flex-row mb-1 ms-auto">
            <a href="<?= base_url('content/list'); ?>" type="submit" class="btn btn-danger ms-2">Cancel</a>
            <button type="submit" class="btn btn-success ms-2">Save</button>
            <?= isset($post) ? view('templates/preview_btn', ['link' => base_url('content/view/'.$post['id'])]) : '' ?>
        </div>
        <!-- Page title -->
        <div class="mb-3">
            <?= form_label('Page Title', '', [
                'class' => 'form-label',
                'for' => 'page-title'
            ]); ?>
            <?= form_input('page-title', isset($post) ? $post['title'] : '', [
                'class' => 'form-control',
                'id' => 'page-title',
                'required' => ''
            ], 'text'); ?>
        </div>
        <!-- Page publish time -->
        <div class="mb-3">
            <?= form_label('Page Publish Time', '', [
                'class' => 'form-label',
                'for' => 'page-publish-time'
            ]); ?>
                <?= form_input('page-publish-time', isset($post) ? $post['published_time'] : '', [
                'class' => 'form-control',
                'id' => 'page-publish-time',
                'required' => ''
            ], 'date'); ?>
        </div>
        <!-- Page Activation Status -->
        <div class="mb-3">
            <?= form_label("Is Active", "", [
            'class' => 'form-label',
            'for' => 'page-is-active'
        ]); ?>
            <?= form_dropdown(
            "page-is-active", 
            [
                1 => 'Active',
                0 => 'Deactivate'
            ],
            isset($post) ? $post['is_active'] : '', 
            [
                'class' => 'form-select',
                'id' => 'page-is-active',
                'required' => ''
            ]);
        ?>
        </div>
        <!-- Page cover page pictures -->
        <div class="mb-3">
            <label for="c-image" class="form-label">Cover Image</label>
            <button type="button" class="btn btn-outline-primary" id="btn-add-image" onclick="$('#page-cover').click();">Add Image</button>
            <input type="file" class="d-none" id="page-cover" name="page-cover[]" class="form-control" accept="image/*" multiple/>
            <div class="mt-3 preview-images-zone" id="preview-images-zone"></div>
        </div>
        <!-- Page Content -->
        <div class="mb-3">
            <label for="wysiwyg-editor" class="form-label">Content</label>
            <textarea id="wysiwyg-editor" class="form-control"></textarea>
        </div>
    </form>
</div>

<script type="text/javascript">
<?php
    if(isset($post))
    {
        $imgSrc = explode(',', $post['cover']);
        foreach($imgSrc as $src)
        {
            if(!empty($src))
            {
?>
addPreviewImage('<?= base_url($src) ?>');
saveToDict(num - 1, '<?= base_url($src); ?>');
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

    const editor = SUNEDITOR.create((document.getElementById('wysiwyg-editor')), {
        buttonList: [
            ['undo', 'redo'],
            ['font', 'fontSize', 'formatBlock'],
            ['paragraphStyle', 'blockquote'],
            ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript'],
            ['fontColor', 'hiliteColor', 'textStyle'],
            ['removeFormat'],
            '/',
            ['outdent', 'indent'],
            ['align', 'horizontalRule', 'list', 'lineHeight'],
            ['table', 'link', 'image', 'video', 'audio'],
            ['fullScreen', 'showBlocks', 'codeView'],
            ['preview', 'print'],
            ['save', 'template'],
        ],
        height: 'auto',
        width: 'auto',
        charCounter: true
    });
    editor.setDefaultStyle('font-family:Arial;font-size:12px');
    // Set content to the editor
    editor.setContents('<?= isset($post) ? $post['content'] : ''; ?>');
    // Submit listener
    $("form#page-form").submit(function(e) {
        e.preventDefault();
        
        var fd = new FormData();
        fd.append("page-title", $("#page-title").val());
        fd.append("page-publish-time", $("#page-publish-time").val());
        fd.append("page-is-active", $("#page-is-active").val());
        fd.append("page-content", editor.getContents(true));
        fd.append("page-cover-count", imgDict.length);
        $.map(imgDict, function(f, idx){
            fd.append("page-cover-" + idx, f.file);
        });

        $.post({
            url: '<?= isset($post) ? base_url('api/content/edit/'.$post['id']) : base_url('api/content/add'); ?>',
            headers: {
                'Authorization': 'Bearer ' + $.cookie('<?= session()->get('token_access_key') ?>')
            },
            dataType: 'json',
            contentType:false,
            processData:false,
            data: fd,
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
                    console.log(e);
                    $r = $.parseJSON(e.responseText);
                    if($r.validate_error) {
                        $m = $.parseJSON($r.msg);
                        $.each($m, function(k, v){
                            toastError(v);
                        });
                    }
                    else toastError($r.msg);
                }
            },
        });
    });
});
</script>