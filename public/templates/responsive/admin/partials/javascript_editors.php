<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/Sortable/Sortable.min.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/dropzone/dist/min/dropzone.min.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/simplemde/dist/simplemde.min.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/summernote/dist/summernote.min.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/corejs-typeahead/dist/typeahead.bundle.min.js"></script>
<script type="text/javascript">
    // Disable dropzone auto discover...
    Dropzone.autoDiscover = false;
</script>

<script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    var goteo = goteo || {};
    goteo.texts = goteo.texts || {};
    <?php foreach(['bold', 'italic', 'strikethrough', 'heading', 'smaller_heading', 'bigger_heading', 'code', 'quote', 'generic_list', 'numbered_list', 'create_link', 'insert_image', 'insert_table', 'insert_horizontal_line', 'toggle_preview', 'toggle_side_by_side', 'toggle_fullscreen', 'markdown_guide', 'undo', 'redo', 'close'] as $key): ?>
        goteo.texts['form-editor-<?= $key ?>'] = '<?= $this->ee($this->text('form-editor-' . $key), 'js') ?>';
    <?php endforeach ?>
    goteo.texts['form-dragndrop-unsupported'] = '<?= $this->ee($this->text('form-dragndrop-unsupported'), 'js') ?>';
    goteo.texts['admin-projects'] = '<?= $this->ee($this->text('admin-projects'), 'js') ?>';
    goteo.texts['admin-calls'] = '<?= $this->ee($this->text('admin-calls'), 'js') ?>';
    goteo.texts['admin-matchers'] = '<?= $this->ee($this->text('admin-matchers'), 'js') ?>';
    goteo.texts['admin-users'] = '<?= $this->ee($this->text('admin-users'), 'js') ?>';
    goteo.texts['admin-channels'] = '<?= $this->ee($this->text('admin-channels'), 'js') ?>';
    goteo.texts['admin-consultants'] = '<?= $this->ee($this->text('admin-consultants'), 'js') ?>';

    goteo.typeahead_engines = goteo.typeahead_engines || {};

    // Forms process
    $(function(){
        $('.autoform.hide-help .help-text').attr('data-desc', '<?= $this->ee($this->text('translator-original-text'), 'js') ?>: ');
        $('.autoform.hide-help .help-text').wrapInner('<span></span>').prepend('<i class="fa fa-clipboard"></i> ');

        var clipboard = new Clipboard('.autoform .help-text .fa-clipboard', {
            text: function(trigger) {
                return $(trigger).next('span').html();
            }
        });

        clipboard.on('success', function(e){
            // console.log('success', e.action, e.trigger, e.text);
            $(e.trigger).tooltip({
                title: '<?= $this->ee($this->text('regular-copied'), 'js') ?>',
                trigger: 'manual'
            }).tooltip('show');
            setTimeout(function() {
                $(e.trigger).tooltip('destroy');
            }, 1000);
        });
        clipboard.on('error', function(e){
            console.log('Operation not supported on your browser', e.action, e.trigger);
        });
    }, 10);
    // @license-end

</script>
<!-- POST PROCESSING THIS JAVASCRIPT BY GRUNT -->
<!-- build:js assets/js/editors.js -->
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/typeahead/engine.projects.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/typeahead/engine.calls.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/typeahead/engine.users.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/typeahead/engine.consultants.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/typeahead/engine.channels.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/typeahead/engine.matchers.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/forms.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/charts/d3-discrete-values.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/charts/d3-pie.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/charts/d3-timemetrics.js"></script>
<!-- endbuild -->


