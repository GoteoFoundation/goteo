<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Application\Message;

$project = $vars['project'];

if (!$project instanceof Model\Project) {
    Message::error('Instancia de proyecto corrupta');
    throw new Redirection('/admin/projects');
}

$conf = $vars['conf'];
?>
<script type="text/javascript">
function disableRound2() {
    var check = document.getElementById("oneround");
    if (check.checked) {
        $(".round2").hide();
    } else {
        $(".round2").show();
    }
}
</script>

<div class="widget">
    <p>Configure aquí los días que durará cada ronda.</p>

    <form method="post" action="/admin/projects" >
        <input type="hidden" name="id" value="<?php echo $project->id ?>" />

    <p>
        <label for="oneround">
            <input type="checkbox" onchange="disableRound2();" id="oneround" name="oneround" value="1" <?php if ($conf->one_round) echo 'checked="checked"'; ?>/>¿Una sola ronda?
        </label>
    </p>

    <p>
        <label for="round1">Primera ronda:</label><br />
        <input type="text" id="round1" name="round1" value="<?php echo $conf->days_round1; ?>" style="width: 150px;"/>
    </p>

    <p class="round2">
        <label for="round2">Segunda ronda:</label><br />
        <input type="text" id="round2" name="round2" value="<?php echo $conf->days_round2; ?>" style="width: 150px;"/>
    </p>

        <input type="submit" name="save-rounds" value="Guardar" />

    </form>
</div>

<script type="text/javascript">
disableRound2();
</script>
