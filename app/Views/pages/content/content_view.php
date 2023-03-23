<?php
    $postModel = new \App\Models\Post();
    $post = $postModel->select(
        'title, content'
    )->find($id);

    if(is_null($post))
    {
?>
<!-- Stop render and going back to list -->
<script type="text/javascript">
    alert("The selected post is no longer exist!");
    window.location.href = '<?= base_url('content/list'); ?>';
</script>
<?php
    }
?>

<div class="container-fluid">
    <br/>
    <h4 class="fw-bold"><?= isset($post) ? $post['title']: ''; ?></h4>
    <?= isset($post) ? $post['content']: ''; ?>
</div>