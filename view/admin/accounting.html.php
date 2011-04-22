<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Administración de transacciones</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <p><?php echo $this['content']; ?></p>

            <?php if (!empty($this['projects'])) :
                foreach ($this['projects'] as $project) : ?>
                    <p><?php echo $project->name; ?></p>
                    <p><pre><?php echo print_r($project->investors, 1); ?></pre></p>
                    <hr />
            <?php endforeach;
                endif;?>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';