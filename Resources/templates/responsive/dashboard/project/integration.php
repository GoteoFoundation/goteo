<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">

    <div class="panel section-content spacer">

        <h5 class="spacer-20"><i class="fa fa-key"></i> <?= $this->text('dashboard-project-integration-token') ?></h5>
        <div class="panel-body">
            <?= $this->token ?>
        </div>

        <h5 class="spacer-20"><i class="fa fa-link"></i> <?= $this->text('dashboard-project-integration-link') ?></h5>
        <div class="panel-body">
            <a href="//<?= $this->url ?>"> <?= $this->url ?> </a>
        </div>

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
