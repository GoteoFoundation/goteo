<?php
//vista para poner un widget de proyecto en un iframe

use Goteo\Core\View;

$bodyClass = 'project-embed'; include __DIR__ . '/../prologue.html.php' ?>

<div class="alone-project">
    <?php echo View::get('project/widget/project.html.php', $vars); ?>
</div>

<?php include __DIR__ . '/../epilogue.html.php' ?>
