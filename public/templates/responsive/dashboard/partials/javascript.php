<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/Sortable/Sortable.min.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/dropzone/dist/min/dropzone.min.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/simplemde/dist/simplemde.min.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/typeahead.js/dist/typeahead.bundle.min.js"></script>
<script type="text/javascript">
    // Disable dropzone auto discover...
    Dropzone.autoDiscover = false;
</script>

<script type="text/javascript">
    goteo = goteo || {};
    goteo.texts = goteo.texts || {};
    <?php foreach(['bold', 'italic', 'strikethrough', 'heading', 'smaller_heading', 'bigger_heading', 'code', 'quote', 'generic_list', 'numbered_list', 'create_link', 'insert_image', 'insert_table', 'insert_horizontal_line', 'toggle_preview', 'toggle_side_by_side', 'toggle_fullscreen', 'markdown_guide', 'undo', 'redo'] as $key): ?>
        goteo.texts['form-editor-<?= $key ?>'] = '<?= $this->ee($this->text('form-editor-' . $key), 'js') ?>';
    <?php endforeach ?>

</script>
<!-- POST PROCESSING THIS JAVASCRIPT BY GRUNT -->
<!-- build:js assets/js/dashboard.js -->
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/forms.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/dashboard/ajax-utils.js"></script>
<!-- endbuild -->


