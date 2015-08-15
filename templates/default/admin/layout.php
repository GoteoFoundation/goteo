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

            <div id="admin-content">
                <?= $this->supply('admin-content') ?>
            </div>

            <?= $this->supply('admin-aside') ?>

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
    .label.label-admin,.label.label-pending,.label.label-info {
        background: #5BC0DE; /* blue */
    }
    .label.label-superadmin,.label.label-sending,.label.label-warning {
        background: #F0AD4E; /* yellow */
    }
    .label.label-sent,.label.label-success {
        background: #5CB85C; /* green */
    }
    .label.label-root,.label.label-failed,.label.label-inactive,.label.label-error {
        background: #D9534F; /* red */
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

    .admin .widget ul.pagination {
        border-top: 1px solid #f0f0f0;
        padding-top: 5px;
    }
    .admin .widget ul.pagination li,
    .admin .widget ul.pagination li.selected {
        padding: 0;
        margin: 0;
    }
    .admin .widget ul.pagination ul li a {
        background: #f0f0f0;
    }

</style>
<?php $this->append() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
$(function(){
    $('#select-node').change(function(e){
        e.preventDefault();
        location.search = '?admin_node=' + $(this).val();
    });
});
</script>
<?php $this->append() ?>
