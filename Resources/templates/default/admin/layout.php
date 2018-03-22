<?php
/*
    Base layout for admin
 */

$this->layout('layout', [
    'bodyClass' => 'admin',
    'sidebarClass' => 'yellow',
    'jsreq_autocomplete' => true,
    'jquery' => 'latest' // Use the latest jquery release please...
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

<link rel="stylesheet" href="<?= SRC_URL ?>/assets/vendor/typeahead/jquery.typeahead.min.css" type="text/css" />
<link rel="stylesheet" href="<?= SRC_URL ?>/assets/vendor/datepicker/css/bootstrap.css" type="text/css" />
<link rel="stylesheet" href="<?= SRC_URL ?>/assets/vendor/simplemde/dist/simplemde.min.css" type="text/css" />
<style type="text/css">

    /* Tuning Markdown editor */
    .newsletter, .newsletter a {
        font-size:14px;
    }
    .newsletter .editor-preview a {
        color: #3AB9C2 !important;
        padding:0 !important;
    }
    .newsletter .CodeMirror .CodeMirror-code .cm-link {
        color: #3AB9C2;
    }

    .newsletter .CodeMirror .CodeMirror-code .cm-url {
        color: #7f8c8d;
    }


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
    .label.label-root,.label.label-failed,.label.label-inactive,.label.label-error,.label.label-danger {
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

    .admin .widget {
        overflow: auto;
    }

    .admin .widget table.table{
        width:100%;
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

<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/simplemde/dist/simplemde.min.js"></script>

<?php if($this->debug()): ?>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/typeahead/jquery.typeahead.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/datepicker/js/zebra_datepicker.src.js"></script>
<?php else: ?>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/typeahead/jquery.typeahead.min.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/datepicker/js/zebra_datepicker.js"></script>
<?php endif ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function(){
    $('#select-node').change(function(e){
        e.preventDefault();
        location.search = '?admin_node=' + $(this).val();
    });

    $('input.datepicker').Zebra_DatePicker({
        days: ['<?= $this->ee($this->text('date-sunday'), 'js') ?>', '<?= $this->ee($this->text('date-monday'), 'js') ?>', '<?= $this->ee($this->text('date-tuesday'), 'js') ?>', '<?= $this->ee($this->text('date-wednesday'), 'js') ?>', '<?= $this->ee($this->text('date-thursday'), 'js') ?>', '<?= $this->ee($this->text('date-friday'), 'js') ?>', '<?= $this->ee($this->text('date-saturday'), 'js') ?>'],
        days_abbr: ['<?= $this->ee($this->text('date-su'), 'js') ?>', '<?= $this->ee($this->text('date-mo'), 'js') ?>', '<?= $this->ee($this->text('date-tu'), 'js') ?>', '<?= $this->ee($this->text('date-we'), 'js') ?>', '<?= $this->ee($this->text('date-th'), 'js') ?>', '<?= $this->ee($this->text('date-fr'), 'js') ?>', '<?= $this->ee($this->text('date-sa'), 'js') ?>'],
        months: ['<?= $this->ee($this->text('date-january'), 'js') ?>', '<?= $this->ee($this->text('date-february'), 'js') ?>', '<?= $this->ee($this->text('date-march'), 'js') ?>', '<?= $this->ee($this->text('date-april'), 'js') ?>', '<?= $this->ee($this->text('date-may'), 'js') ?>', '<?= $this->ee($this->text('date-june'), 'js') ?>', '<?= $this->ee($this->text('date-july'), 'js') ?>', '<?= $this->ee($this->text('date-august'), 'js') ?>', '<?= $this->ee($this->text('date-september'), 'js') ?>', '<?= $this->ee($this->text('date-october'), 'js') ?>', '<?= $this->ee($this->text('date-november'), 'js') ?>', '<?= $this->ee($this->text('date-december'), 'js') ?>'],
        months_abbr: ['<?= $this->ee($this->text('date-jan'), 'js') ?>', '<?= $this->ee($this->text('date-feb'), 'js') ?>', '<?= $this->ee($this->text('date-mar'), 'js') ?>', '<?= $this->ee($this->text('date-apr'), 'js') ?>', '<?= $this->ee($this->text('date-may'), 'js') ?>', '<?= $this->ee($this->text('date-jun'), 'js') ?>', '<?= $this->ee($this->text('date-jul'), 'js') ?>', '<?= $this->ee($this->text('date-aug'), 'js') ?>', '<?= $this->ee($this->text('date-sep'), 'js') ?>', '<?= $this->ee($this->text('date-oct'), 'js') ?>', '<?= $this->ee($this->text('date-nov'), 'js') ?>', '<?= $this->ee($this->text('date-dec'), 'js') ?>'],
        show_select_today: '<?= $this->ee($this->text('date-today'), 'js') ?>'
    });

});
// @license-end
</script>
<?php $this->append() ?>
