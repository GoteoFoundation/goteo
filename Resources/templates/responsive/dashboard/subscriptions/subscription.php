<?php $this->layout('dashboard/layout') ?>
<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
    <div class="inner-container">
        <?php var_dump($this->subscription) ?>
    </div>
</div>

<?php $this->replace() ?>