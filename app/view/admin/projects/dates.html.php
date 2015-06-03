<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Application\Message;

$project = $vars['project'];

if (!$project instanceof Model\Project) {
    Message::Error('Instancia de proyecto corrupta');
    throw new Redirection('/admin/projects');
}

$elements = array(
    'created' => array(
        'type'      => 'datebox',
        'title'     => 'Fecha de creación',
        'value'     => !empty($project->created) ? $project->created : null
    ),
    'updated' => array(
        'type'      => 'datebox',
        'title'     => 'Fecha de enviado a revisión',
        'value'     => !empty($project->updated) ? $project->updated : null
    ),
    'published' => array(
        'type'      => 'datebox',
        'title'     => 'Fecha de inicio de campaña',
        'subtitle'  => '(Segun esta fecha se calculan los días)',
        'value'     => !empty($project->published) ? $project->published : null
    ),
    'passed' => array(
        'type'      => 'datebox',
        'title'     => 'Fecha de paso a segunda ronda',
        'subtitle'  => '(marca fin de primera ronda)',
        'value'     => !empty($project->passed) ? $project->passed : null
    ),
    'success' => array(
        'type'      => 'datebox',
        'title'     => 'Fecha de éxito',
        'subtitle'  => '(marca fin de segunda ronda)',
        'value'     => !empty($project->success) ? $project->success : null
    ),
    'closed' => array(
        'type'      => 'datebox',
        'title'     => 'Fecha de cierre',
        'value'     => !empty($project->closed) ? $project->closed : null
    )

);
?>
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

    <form method="post" action="/admin/projects" >
        <input type="hidden" name="id" value="<?php echo $project->id ?>" />

<?php foreach ($elements as $id=>$element) : ?>
    <div id="<?php echo $id ?>">
        <h4><?php echo $element['title'] ?>:</h4>
        <?php echo View::get('superform/element/datebox.html.php', array('value'=>$element['value'], 'id'=>$id, 'name'=>$id, 'js' => true)); ?>
        <?php if (!empty($element['subtitle'])) echo $element['subtitle'].'<br />'; ?>
    </div>
        <br />
<?php endforeach; ?>

        <input type="submit" name="save-dates" value="Guardar" />

    </form>
</div>
