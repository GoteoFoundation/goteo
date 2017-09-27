<?php $this->layout('dashboard/project/translate/layout') ?>

<?php $this->section('dashboard-translate-project') ?>
<blockquote class="padding-right">
    <?= $this->text('dashboard-translate-project-translating', ['%LANG%' => '<strong><em>' . $this->languages[$this->lang] . '</em></strong>', '%ORIGINAL%' => '<strong><em>' . $this->languages[$this->project->lang] . '</em></strong>']) ?>

    <?= $this->insert('dashboard/partials/translate_menu', [
        'base_link' => '/dashboard/project/' .  $this->project->id . '/translate/' . $this->step . '/',
        'languages' => $this->languages,
        'translated' => $this->translated,
        'lang' => $this->lang,
        'class' => 'pull-right',
        'skip' => [$this->project->lang],
        'exit_link' => '/dashboard/project/' .  $this->project->id . '/translate'
    ]) ?>
</blockquote>

<?= $this->form_form($this->raw('form')) ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    $(function(){
        $('.autoform .help-text').attr('data-desc', '<?= $this->ee($this->text('translator-original-text'), 'js') ?>: ');
    });
    // @license-end
</script>
<?php $this->append() ?>
