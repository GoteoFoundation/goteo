<?php $this->layout('admin/projects/edit_layout') ?>

<?php $this->section('admin-project-content') ?>

    <p>Configure aquí los días que durará cada ronda.</p>

    <form method="post" action="/admin/projects/conf/<?= $this->project->id ?>" >

    <p>
        <label for="oneround">
            <input type="checkbox" onchange="disableRound2();" id="oneround" name="oneround" value="1" <?php if ($this->conf->one_round) echo 'checked="checked"' ?>/>¿Una sola ronda?
        </label>
    </p>

    <p>
        <label for="round1">Primera ronda:</label><br />
        <input type="text" id="round1" name="round1" value="<?= $this->conf->days_round1 ?>" style="width: 150px;"/>
    </p>

    <p class="round2">
        <label for="round2">Segunda ronda:</label><br />
        <input type="text" id="round2" name="round2" value="<?= $this->conf->days_round2 ?>" style="width: 150px;"/>
    </p>

        <input type="submit" name="save-rounds" value="Guardar" />

    </form>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

function disableRound2() {
    var check = document.getElementById("oneround");
    if (check.checked) {
        $(".round2").hide();
    } else {
        $(".round2").show();
    }
}

disableRound2();

// @license-end
</script>

<?php $this->append() ?>
