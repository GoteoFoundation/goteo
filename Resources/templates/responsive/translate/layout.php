<?php

$this->layout("layout", []);


$this->section('content');

?>

<div class="container">

    <div class="row row-form">
        <div class="panel panel-default">

            <?= $this->insert('translate/partials/selector') ?>

            <div class="panel-body">
            <?php $this->section('translate-content') ?>
            <?php $this->stop() ?>
            </div>
        </div>
    </div>
</div>

<?php $this->replace() ?>


<?php $this->section('head') ?>
    <?php $this->section('translate-head') ?>
    <?php $this->stop() ?>
<?php $this->append() ?>

<?php $this->section('footer') ?>
    <script type="text/javascript">
        $(function(){
            $('select#select-zone').on('change', function(e) {
                e.preventDefault();
                if($(this).val())
                    window.location = '/translate/' + $(this).val();
                else
                    window.location = '/translate';
            });
            $('select#select-pending').on('change', function(e) {
                e.preventDefault();
                $(this).closest('form').submit();
            });
        });
    </script>
    <?php $this->section('translate-footer') ?>
    <?php $this->stop() ?>
<?php $this->append() ?>
