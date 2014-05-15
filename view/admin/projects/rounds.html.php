<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Library\Message;

$project = $this['project'];

if (!$project instanceof Model\Project) {
    Message::Error('Instancia de proyecto corrupta');
    throw new Redirection('/admin/projects');
}

$conf = $this['conf'];
?>
<div class="widget">
    <p>Configure aquí los días que durará cada ronda.</p>

    <form method="post" action="/admin/projects" >
        <input type="hidden" name="id" value="<?php echo $project->id ?>" />

    <p>
        <label for="round1">Primera ronda:</label><br />
        <input type="text" id="round1" name="round1" value="<?php echo $conf->days_round1; ?>" style="width: 475px;"/>
    </p>

    <p>
        <label for="round2">Segunda ronda:</label><br />
        <input type="text" id="round2" name="round2" value="<?php echo $conf->days_round2; ?>" style="width: 475px;"/>
    </p>

        <input type="submit" name="save-rounds" value="Guardar" />

    </form>
</div>
