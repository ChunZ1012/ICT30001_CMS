<?= isset($comment) ? $comment : "" ; ?>
<?= '<div class="mb-3">'; ?>
<?= 
    form_label(
        $label, "", [
        'class' => 'form-label',
        'for' => $id
    ]);
?>
<?=
    form_dropdown(
        $id,
        $select_options,
        $active,
        [
            'class' => 'form-select',
            'id' => $id,
            'required' => $required,
    ]);
?>
<?= '<label class="invalid-feedback error" for="'.$id.'" generated="true"></label>'; ?>
<?= '</div>'; ?>