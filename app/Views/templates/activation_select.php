<?= isset($comment) ? $comment : "" ; ?>
<?= '<div class="mb-3">'; ?>
<?= 
    form_label(
        "Is Active", "", [
        'class' => 'form-label',
        'for' => $id
    ]);
?>
<?=
    form_dropdown(
        "page-is-active",
        $select_options,
        $active,
        [
            'class' => 'form-select',
            'id' => $id,
            'required' => $required,
    ]);
?>
<?= '<div class="invalid-feedback"></div>'; ?>
<?= '</div>'; ?>