<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Administración de transacciones</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <?php if (!empty($this['projects'])) : ?>
            
<!--                <p>Por ahora manualmente <a href="?execute">ejecutar los cargos</a></p>   -->

                <?php foreach ($this['projects'] as $project) :

                    echo $project->name . '<br />';
                    echo '<pre>' . print_r($project->investors, 1) . '</pre>';
                    echo '<hr />';
                continue;
                    ?>
                    <h3><?php echo $project->name . ' ' . $this['status'][$project->status]; ?></h3>
                    <?php foreach ($project->investors as $key=>$investor) : $errors = array();?>
                        <p><?php echo $investor['name']; ?><?php if ($investor['anonymous']) echo ' (A)'; ?>: <?php echo $investor['amount']; ?> &euro; Payment: <?php echo $investor['status']; ?> <a href="?details=<?php echo $investor['invest']->id; ?>">[Detalles]</a></p>
                    <?php endforeach; ?>
                    <br />
            <?php endforeach; ?>
            <?php else : ?>
                <p>No hay aportes en los proyectos publicados o financiados.</p>
            <?php endif;?>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';