<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">

    <h1><?= $this->text('api-key-tool-tip')?></h1>

    <div class="panel section-content spacer">

        <h5><i class="fa fa-user"></i> <?= $this->text('api-key-user') ?></h5>
        <div class="panel-body">
            <?= $this->user_id ?>
        </div>

        <h5 class="spacer-20"><i class="fa fa-key"></i> <?= $this->text('api-key-key') ?></h5>
        <div class="panel-body">
            <?= $this->key ? $this->key : $this->text('api-key-no-generated') ?>
        </div>

        <div class="spacer-20">
            <?= $this->form_form($this->raw('form')) ?>
        </div>

        <h4 class="spacer"><a href="//developers.goteo.org" target="_blank"><i class="fa fa-book"></i> <?= $this->text('api-key-documentation')?></a></h4>
    </div>


  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
    $(function(){
        $('form.autoform').on('submit', function(){
            if(confirm('<?= $this->ee($this->text('api-key-generate-new-confirm'), 'js') ?>')) {
                return true;
            }
            return false;
        });
    });
</script>
<?php $this->append() ?>
