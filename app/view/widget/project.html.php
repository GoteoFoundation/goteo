<?php
//vista para poner un widget de proyecto en un iframe

use Goteo\Core\View;

$bodyClass = 'project-embed'; include 'view/prologue.html.php' ?>

<div class="alone-project">
    <?php echo new View('view/project/widget/project.html.php', $this); ?>
</div>

<?php include 'view/epilogue.html.php' ?>