<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-sidebar-header') ?>

    <?= $this->insert('project/widgets/micro', ['project' => $this->project, 'admin' => $this->admin]) ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){

<?php if($this->query_has('validate')): ?>
    // $('input[value=""],input:not([value])').first().focus();
    var $group = $('.form-group.has-error:first');
    if($group.length) {
        $input = $group.find('input,select,textarea[name]');
        // console.log($group, $input.attr('id'), $input.val());
        if($group.find('div.markdown').length) {
            // console.log('MD',form.markdowns[$input.attr('id')]);
            form.markdowns[$input.attr('id')].codemirror.focus();
        } else {
            $input.focus();
        }
        $('html, body').animate({
            scrollTop: $group.offset().top
        }, 800);
    }
<?php endif ?>

});

// @license-end
</script>
<?php $this->append() ?>
