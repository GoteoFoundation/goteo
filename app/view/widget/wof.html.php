<?php
// vista para poner un wall of friends en un iframe

use Goteo\Core\View;

$bodyClass = 'project-embed'; include __DIR__ . '/../prologue.html.php' ?>

<div class="alone-wof">
    <?php echo View::get('project/widget/wof.html.php', $vars); ?>
</div>

<?php include __DIR__ . '/../epilogue.html.php' ?>
