<?php

$this->layout('dashboard/layout');

$this->section('dashboard-content');

?>

<div class="container">
    <h1><?= $this->text('dashboard-menu-projects') ?></h1>
    <div class="row general-dashboard">
        <div class="col-sm-3 col-xs-12 dashboard-sidebar">
            <?= $this->supply('dashboard-project-menu', $this->insert('dashboard/project/partials/menu')) ?>
        </div>
        <div class="col-sm-9 col-xs-12">
            <?= $this->supply('dashboard-project-content') ?>
        </div>
    </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
    $(function(){
    $('.dashboard-sidebar').affix({
      offset: {
        top: $('.general-dashboard').offset().top
        // ,bottom: function () {
        //   return (this.bottom = $('#footer').outerHeight(true))
        // }
      }
    })
    });
</script>
<?php $this->append() ?>
