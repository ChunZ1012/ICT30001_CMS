<head>
    <!-- <link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script> -->
    <!-- SunEditor -->
    <link href="/css/suneditor.min.css" rel="stylesheet" />
    <script src="/js/suneditor.min.js"></script>
</head>

<?php
    // $parsed_url = parse_Url(current_url());
    // $path = $parsed_url['path'];
    // $id = substr($path, strrpos($path, "/") + 1);

    $postModel = new \App\Models\Post();
    if($is_add) $post = $postModel->find(-1);
    else
    {
        $post = $postModel->select(
            'id, title, date_format(published_time, "%Y-%m-%d") as published_time, is_active, content'
        )->find($id);

        if(is_null($post) || empty($post)) 
        {
            echo '<script>alert("The selected post is no longer exist!");window.location.href = "'.base_url('content/list').'"</script>';    
        }
    }
?>

<div class="row">
    <form action="post" id="content-form">
        <div class="d-flex flex-column">
            <!-- Action buttons -->
            <div class="d-flex flex-row mb-1 ms-auto">
                <a href="<?= base_url('content/list'); ?>" type="submit" class="btn btn-danger ms-1 me-1">Cancel</a>
                <button type="submit" class="btn btn-success mx-1">Save</button>
                <button type="button" id="preview-btn" class="btn btn-primary mx-1" onclick="javascript:void(0)">Preview</button>
            </div>
            <!-- Page title -->
            <div class="mb-3">
                <label for="page-title" class="form-label">Page Title</label>
                <input type="text" class="form-control" id="page-title" name="page-title" value="<?= isset($post['title']) ? $post['title'] : ''; ?>" required />
            </div>
            <!-- Page publish time -->
            <div class="mb-3">
                <label for="page-publish-time" class="form-label">Page Publish Time</label>
                <input type="date" class="form-control" id="page-publish-time" name="page-publish-time" value="<?= isset($post['published_time']) ? $post['published_time'] : ''; ?>" required />
            </div>
            <div class="mb-3">
                <label for="page-is-active" class="form-label">Is Active</label>
                <select class="form-select" name="page-is-active" id="page-is-active" required>
                    <option value="1" <?= (isset($post['is_active']) ? ($post['is_active'] == 1 ? 'selected': '') : 'selected') ?>>Active</option>
                    <option value="0" <?= isset($post['is_active']) ? ($post['is_active'] == 0 ? 'selected': '') : '' ?>>Deactive</option>
                </select>
            </div>
            <!-- Page Content -->
            <div class="mb-3">
                <label for="wysiwyg-editor" class="form-label">Content</label>
                <textarea id="wysiwyg-editor" class="form-control"></textarea>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
$(function() {
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
        minHeight: '250px',
        maxHeight: '350px',
        charCounter: true
    });
    editor.setDefaultStyle('font-family:Arial;font-size:12px');
    // Set content to the editor
    editor.setContents('<?= isset($post['content']) ? $post['content'] : ''; ?>');
    // Submit listener
    $("form#content-form").submit(function(e) {
        e.preventDefault();
        var data = {
            "data": {
                title: $("#page-title").val(),
                publish_time: $("#page-publish-time").val(),
                is_active: $("#page-is-active").val(),
                content: editor.getContents(true)
            }
        }

        $.post({
            url: '<?= is_null($post) ? base_url('api/content/add') : base_url('api/content/edit/'.$post['id']); ?>',
            // TODO: Get the jwt token from session storage
            headers: {
                'Authorization' : 'Bearer '
            },
            dataType: 'application/json',
            data: JSON.stringify(data),
            statusCode: {
                200: (r) => {
                    if (!r.error) {
                        alert('Successfully saved!');
                    } else {
                        alert('Error when saving the content!\n' + r.msg);
                    }
                },
                400: (r) => {
                    var res = JSON.parse(r.responseText);
                    alert(res.msg);
                },
                401: () => {
                    alert('Please login before continue!');
                },
                500: (r) => {
                    alert("Internal server error! Please try again later")
                }
            }
        });
    });

    $("#preview-btn").click(function(e){
        e.preventDefault();
        window.location.href='<?= base_url('content/view/'.$id); ?>';
    });
});
</script>