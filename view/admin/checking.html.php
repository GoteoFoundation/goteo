<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Revisión de proyectos</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>

            <ul>
            <?php foreach ($this['projects'] as $project) : ?>
                <li>
                    <label><?php echo $project->name; ?>:</label>
                    <a href="/project/<?php echo $project->id; ?>" target="_blank">[Ver]</a>
                </li>
            <?php endforeach; ?>
            </ul>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';