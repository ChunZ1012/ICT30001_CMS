<head>
    <!-- SunEditor -->
    <link href="/css/suneditor/suneditor.min.css" rel="stylesheet" />
    <script src="/js/suneditor/suneditor.min.js"></script>
</head>


<?php
    $is_edit = isset($id) && $id > 0;
    if($is_edit)
    {
        $postModel = new \App\Models\Post();
        $post = $postModel->select(
            'id, title, date_format(published_time, "%Y-%m-%d") as published_time, is_active, content'
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
    <form class="d-flex flex-column" id="page-form" method="post">
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
                <?= form_input('page-publish-time', isset($post) ? $post    ['published_time'] : '', [
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
        <!-- Page Content -->
        <div class="mb-3">
            <label for="wysiwyg-editor" class="form-label">Content</label>
            <textarea id="wysiwyg-editor" class="form-control"></textarea>
        </div>
    </form>
</div>

<script type="text/javascript">
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
            '/', // Line break
            ['outdent', 'indent'],
            ['align', 'horizontalRule', 'list', 'lineHeight'],
            ['table', 'link', 'image', 'video',
                'audio' /** ,'math' */
            ], // You must add the 'katex' library at options to use the 'math' plugin.
            /** ['imageGallery'] */ // You must add the "imageGalleryUrl".
            ['fullScreen', 'showBlocks', 'codeView'],
            ['preview', 'print'],
            ['save', 'template'],
        ],
        height: 'auto',
        width: 'auto',
        // minHeight: '250px',
        // maxHeight: '350px',
        charCounter: true
    });
    editor.setDefaultStyle('font-family:Arial;font-size:12px');
    // Set content to the editor
    editor.setContents('<?= isset($post) ? $post['content'] : ''; ?>');
    // Submit listener
    $("form#page-form").submit(function(e) {
        e.preventDefault();
        var data = {
            "data": {
                "page-title": $("#page-title").val(),
                "page-publish-time": $("#page-publish-time").val(),
                "page-is-active": $("#page-is-active").val(),
                "page-content": editor.getContents(true)
            }
        }

        $.post({
            url: '<?= isset($post) ? base_url('api/content/edit/'.$post['id']) : base_url('api/content/add'); ?>',
            headers: {
                'Authorization': 'Bearer ' + $.cookie('<?= session()->get('token_access_key') ?>')
            },
            dataType: 'json',
            contentType:'application/json',
            data: JSON.stringify(data),
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
                        });
                    }
                    else toastError($r.msg);
                }
            },
        });
    });
});
</script>