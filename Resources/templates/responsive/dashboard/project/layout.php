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
    $('input,select,textarea[name]').each(function(){
    // $('textarea[name]').each(function(){
        // console.log($(this), $(this).attr('id'), $(this).val());
        if($(this).val() == '') {
            if($(this).closest('div').hasClass('markdown')) {
                // console.log(form.markdowns[$(this).attr('id')]);
                form.markdowns[$(this).attr('id')].codemirror.focus();
            } else {
                $(this).focus();
            }
            $('html, body').animate({
                scrollTop: $(this).closest('.form-group').offset().top
            }, 800);
            return false;
        }
    });
    <?php endif ?>
});

// @license-end
</script>
<?php $this->append() ?>
