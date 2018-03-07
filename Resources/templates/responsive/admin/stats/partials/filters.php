<form class="pronto" method="get" action="<?= $this->get_pathinfo() ?>">
<div class="row table-row">

    <div class="form-group col-xs-6 col-sm-5">
        <label for="filter-from"><?= $this->text('regular-date_in') ?></label>
        <div class="input-group">
            <input type="date" class="form-control" id="filter-from" name="from" value="<?= $this->get_query('from') ?>">
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
    </div>

    <div class="form-group col-xs-6 col-sm-5">
        <label for="filter-to"><?= $this->text('regular-date_out') ?></label>
        <div class="input-group">
            <input type="date" class="form-control" id="filter-to" name="to" value="<?= $this->get_query('to') ?>">
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
