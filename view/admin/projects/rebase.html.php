<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Library\Message;

$project = $this['project'];

if (!$project instanceof Model\Project) {
    Message::Error('Instancia de proyecto corrupta');
    throw new Redirection('/admin/projects');
}


?>
<script type="text/javascript">
    function idverify() {
        if ($('#sub-header').val() == '') {
            alert('No has puesto la nueva id');
            return false;
        } else {
            return true;
        }
    }
</script>
<div class="widget">
<p>
    <?php if (!empty($project->passed)) {
        echo 'El proyecto terminó la primera ronda el día <strong>'.date('d/m/Y', strtotime($project->passed)).'</strong>.';
        if ($project->passed != $project->willpass) {
            echo '<br />Aunque debería haberla terminado el día <strong>'.date('d/m/Y', strtotime($project->willpass)).'</strong>.';
        }
    } else {
        echo 'El proyecto terminará la primera ronda el día <strong>'.date('d/m/Y', strtotime($project->willpass)).'</strong>.';
    } ?>

</p>

    <p>Cambiar las fechas puede causar cambios en los días de campaña del proyecto.</p>

    <form method="post" action="/admin/projects/rebase/<?php echo $project->id; ?>" onsubmit="return idverify();">
        <input type="hidden" name="id" value="<?php echo $project->id ?>" />
        <input type="hidden" name="oldid" value="<?php echo $project->id ?>" />

        <p>
            <label>
                <input type="text" name="newid"  id="newid"
                       
            </label>
        </p>
        <input type="submit" name="proceed" value="rebase" />

    </form>
</div>
