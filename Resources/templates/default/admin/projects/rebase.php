<?php $this->layout('admin/projects/edit_layout') ?>

<?php $this->section('admin-project-content') ?>

    <p>OJO! Cambiar la Id del proyecto afecta a <strong>TODO</strong> lo referente al proyecto!.</p>

    <form method="post" action="/admin/projects/rebase/<?= $this->project->id ?>" onsubmit="return idverify();">
        <input type="hidden" name="id" value="<?= $this->project->id ?>" />
        <input type="hidden" name="oldid" value="<?= $this->project->id ?>" />

        <p>
            <label>Nueva ID para el proyecto:<br />
                <input type="text" name="newid"  id="newid"

            </label>
        </p>

        <?php if ($this->project->status >= 3) : ?>
        <h3>OJO!! El proyecto est&aacute; publicado</h3>
        <p>
            Debes marcar expresamente la siguiente casilla, sino dar&aacute; error por estado de proyecto.<br />
            <label>Marcar que se quiere aplicar aunque el proyecto que no est&aacute; ni en Edici&oacute;n ni en Revisi&oacute;n:<br />
                <input type="checkbox" name="force" value="1" />
            </label>

        </p>
        <?php endif ?>
        <input type="submit" name="proceed" value="rebase" />

    </form>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    function idverify() {
        if ($('#newid').val() == '') {
            alert('No has puesto la nueva id');
            return false;
        } else {
            return true;
        }
    }
// @license-end
</script>

<?php $this->append() ?>
