<?php
//vista para poner un idget de proyecto en un iframe

use Goteo\Core\View;

$bodyClass = 'project-embed'; include 'view/prologue.html.php' ?>

<div class="alone-wof">
    <?php echo new View('view/project/widget/wof.html.php', $this); ?>
</div>

<?php include 'view/epilogue.html.php' ?>