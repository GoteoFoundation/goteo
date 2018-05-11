<?php

// TODO: change to a datepicker-full to allow time chooser
?>
<form class="pronto autoform" method="get" action="<?= $this->get_pathinfo() ?>">
<?php foreach($this->a('hidden') as $k => $v): ?>
    <input type="hidden" name="<?= $k ?>" value="<?= $v ?>">
<?php endforeach ?>
<div class="row table-row">

    <div class="form-group col-xs-6 col-sm-5">
        <label for="filter-from"><?= $this->text('regular-date_in') ?></label>
        <div class="input-group date">
            <input type="date" autocomplete="off" class="form-control -datepicker-full" id="filter-from" autocomplete="off" autocorrect="off" autocapitalize="off" name="from" value="<?= $this->filters['from'] ?>">
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
    </div>

    <div class="form-group col-xs-6 col-sm-5">
        <label for="filter-to"><?= $this->text('regular-date_out') ?></label>
        <div class="input-group date">
            <input type="date" autocomplete="off" class="form-control -datepicker-full" id="filter-to" autocomplete="off" autocorrect="off" autocapitalize="off" name="to" value="<?= $this->filters['to'] ?>">
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
    </div>

    <div class="form-group col-xs-12 col-sm-2 align-bottom">
        <button type="submit" class="btn btn-cyan" name="send"><?= $this->text('regular-submit') ?></button>
    </div>
    <div class="clearfix"></div>

</div>
</form>
