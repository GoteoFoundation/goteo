<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'discover';

include 'view/prologue.html.php';

include 'view/header.html.php' ?>


        <div id="sub-header">
            <div>
                <h2 class="title"><?php echo $this['title']; ?></h2>
            </div>

        </div>

        <div id="main">

            <div class="widget projects">
                <?php foreach ($this['list'] as $project) {
                     echo View::get('project/widget/project.html.php', array(
                            'project' => $project
                            ));
                } ?>
            </div>

            <?php echo View::get('pagination.html.php', $this); ?>
        </div>

        <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>
