<?php
/*
    Base layout for admin
 */

$this->layout('layout', [
    'bodyClass' => 'admin',
    'jsreq_autocomplete' => true,
    ]);
?>

<?php $this->section('sub-header') ?>
    <?= $this->insert('admin/partials/breadcrumb') ?>
<?php $this->replace() ?>

<?php $this->section('content') ?>

        <div id="main">

            <div class="admin-center">

            <?= $this->supply('admin-menu', $this->insert('admin/partials/menu')) ?>

            <?php echo $this->supply('admin-content') ?>

            <?php echo $this->supply('admin-aside') ?>

            </div> <!-- fin center -->

        </div> <!-- fin main -->

<?php $this->replace() ?>

<?php $this->section('head') ?>
<style type="text/css">

    ul.ul-admin>li.selected{
        background-color:#C3DFE1 !important;
        /*font-weight: bold;*/
    }

    .admin-center table tbody tr:hover {
        background-color:#C3DFE1 !important;
    }

    .label {
        font-size: 75%;
        font-weight: 700;
        background: #777;
        color:#fff;
        white-space: nowrap;
        text-align: center;
        vertical-align: baseline;
        border-radius: 0.25em;
        padding: 0.15em 0.6em 0.1em;
    }
    .label.label-admin {
        background: #5BC0DE;
    }
    .label.label-superadmin {
        background: #F0AD4E;
    }
    .label.label-root {
        background: #D9534F;
    }
    .admin .channel {
        float:right;
        position: relative;
    }
    .admin .channel .label {
        font-size: 55%;
        position: absolute;
        top:-15px;
        right: 4px;
    }

</style>
<?php $this->append() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
$(function(){
    $('#select-node').change(function(e){
        e.preventDefault();
        location.search = '?node=' + $(this).val();
    });
});
</script>
<?php $this->append() ?>
