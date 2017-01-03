<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>
<?php

$template = $this->edit;

?>
<p><strong><?= $template->name ?></strong>: <?= $template->purpose ?></p>

<div class="widget board">
    <form method="post" action="/admin/templates/edit/<?= $template->id ?>">
        <input type="hidden" name="group" value="<?= $template->group ?>" />
        <p>
            <label for="tpltitle"><?= $this->text('regular-title') ?>:</label><br />
            <input id="tpltitle" type="text" name="title" size="120" value="<?= $template->title ?>" />
        </p>

        <p class="newsletter">
            <label for="tpltext"><?= $this->text('regular-content') ?>:</label><br />

            <textarea id="tpltext" name="text" cols="100" rows="20"><?= $template->text ?></textarea>
        </p>

        <input type="submit" name="save" value="<?= $this->text('regular-save') ?>" />

        <p>
            <label for="mark-pending"><?= $this->text('mark-pending') ?></label>
            <input id="mark-pending" type="checkbox" name="pending" value="1" />
        </p>

        <p>
            <label for="text-type"><?= $this->text('admin-text-type') ?></label>
            <?= $this->html('select', [
                'value' => $template->type,
                'options' => [
                    'html' => $this->text('admin-text-type-html'),
                    'md' => $this->text('admin-text-type-md')],
                'name' => 'type',
                'attribs' => ['id'=>'text-type']]) ?>
        </p>

    </form>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    $(function(){
        $('#text-type').on('change', function() {
            $(this).closest('form').submit();
        });
        <?php if($template->type === 'md'): ?>
        var simplemde = new SimpleMDE({
            element: $("#tpltext")[0],
            spellChecker: false,
            promptURLs: true
        });
        <?php endif ?>
    });
// @license-end
</script>
<?php $this->append() ?>
