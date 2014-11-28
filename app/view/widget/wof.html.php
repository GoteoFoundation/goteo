<?php
// vista para poner un wall of friends en un iframe

use Goteo\Core\View;

$bodyClass = 'project-embed'; include 'view/prologue.html.php' ?>

<div class="alone-wof">
    <?php echo View::get('project/widget/wof.html.php', $this); ?>
</div>

<?php include 'view/epilogue.html.php' ?>
