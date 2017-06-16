<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

    <div class="row">
        <div class="col-md-6">
            <h1><?= $this->text('dashboard-menu-projects-analytics') ?></h1>
            <form name="analytic-post" method="post">
                <label class="spacer" >
                <?= $this->text('regular-analytics') ?>
                </label>
                <input data-toggle="tooltip" title="<?= $this->text('tooltip-user-analytics') ?>" class="form-control" type="text" name="analytics_id" value="<?= $this->project->analytics_id ?>">
                <label class="spacer" >
                <?= $this->text('regular-facebook-pixel') ?>
                </label>
                <input data-toggle="tooltip" title="<?= $this->text('tooltip-user-facebook-pixel') ?>" class="form-control" type="text" name="facebook_pixel" value="<?= $this->project->facebook_pixel ?>" >
                <div class="row spacer">
                    <div class="col-md-8">
                        <button type="submit" class="btn btn-block btn-success"><?= $this->text('regular-save') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    $(function(){

        $('input[type=text][name=analytics_id]').tooltip({
            placement: "bottom",
            trigger: "focus",
            container: "form"
        });

        $('input[type=text][name=facebook_pixel]').tooltip({
            placement: "bottom",
            trigger: "focus",
            container: "form"
        });

    });

// @license-end
</script>

<?php $this->append() ?>
