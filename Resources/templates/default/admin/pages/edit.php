<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php
$page = $this->page;
?>

<div class="widget board">
    <form method="post" action="/admin/pages/edit/<?= $page->id ?>">

        <p>
            <label for="page-name"><?= $this->text('regular-title') ?>:</label><br />
            <input type="text" name="name" id="page-name" value="<?= $page->name ?>" />
        </p>

        <p>
            <label for="page-description"><?= $this->text('regular-header') ?>:</label><br />
            <textarea name="description" id="page-description" cols="60" rows="4"><?= $page->description ?></textarea>
        </p>

        <p>
            <label for="richtext_content"><?= $this->text('regular-content') ?>:</label><br />
            <textarea id="richtext_content" name="content" cols="100" rows="20"><?= $page->content ?></textarea>
        </p>

        <input type="submit" name="save" value="Guardar" />

        <p>
            <label for="mark-pending"><?= $this->text('mark-pending') ?></label>
            <input id="mark-pending" type="checkbox" name="pending" value="1" />
        </p>

        <p>
            <label for="text-type"><?= $this->text('admin-text-type') ?></label>
            <?= $this->html('select', [
                'value' => $page->type,
                'options' => [
                    'html' => $this->text('admin-text-type-html'),
                    'md' => $this->text('admin-text-type-md')],
                'name' => 'type',
                'attribs' => ['id'=>'text-type']]) ?>
        </p>

    </form>
</div>


<?php $this->replace() ?>


<?php $this->section('head') ?>
    <?php if($page->type === 'html'): ?>
        <script type="text/javascript" src="<?= SRC_URL ?>/view/js/ckeditor/ckeditor.js"></script>
    <?php endif ?>
<?php $this->append() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    $(function(){
        $('#text-type').on('change', function() {
            $(this).closest('form').submit();
        });

    <?php if($page->type === 'md'): ?>
        var simplemde = new SimpleMDE({
            element: $("#richtext_content")[0],
            spellChecker: false,
            promptURLs: true
        });
    <?php elseif($page->type === 'html'): ?>
        // Lanza wysiwyg contenido
        CKEDITOR.replace('richtext_content', {
            toolbar: 'Full',
            toolbar_Full: [
                    ['Source','-'],
                    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
                    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
                    '/',
                    ['Bold','Italic','Underline','Strike'],
                    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
                    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                    ['Link','Unlink','Anchor'],
                    ['Image','Format','FontSize'],
                  ],
            skin: 'kama',
            language: 'es',
            height: '300px',
            width: '730px'
        });
    <?php endif ?>
    });
// @license-end
</script>
<?php $this->append() ?>

