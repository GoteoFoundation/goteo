<?php
//vista para poner un idget de proyecto en un iframe

use Goteo\Core\View;

$bodyClass = 'project-show'; include 'view/prologue.html.php' ?>

<?php echo new View('view/project/widget/project.html.php', $this); ?>

<?php include 'view/epilogue.html.php' ?>