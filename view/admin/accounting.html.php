<?php

use Goteo\Library\Text;

$bodyClass = 'project-show';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Administración de transacciones</h2>
            </div>

            <div class="sub-menu">
                <div class="project-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="needs"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="/cron" target="_blank">Ejecutar cron</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <?php if (!empty($this['projects'])) : ?>
                <?php foreach ($this['projects'] as $project) : ?>
                    <div class="widget">
                        <h3><?php echo $project->name . ' / ' . $this['status'][$project->status]; ?></h3>
                        <?php foreach ($project->invests as $key=>$invest) : $errors = array();?>
                        <p><strong><?php echo $invest->user->name; ?></strong><?php if ($invest->anonymous) echo ' (A)'; ?> <?php echo $invest->amount; ?> &euro; (<?php echo $this['investStatus'][$invest->status]; ?>)  (<?php echo $invest->paypalStatus; ?>) <a href="?details=<?php echo $invest->id; ?>">[Detalles]</a></p>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No hay aportes en los proyectos publicados o financiados.</p>
            <?php endif;?>
        </div>
<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';