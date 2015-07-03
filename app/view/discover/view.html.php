<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'discover';

include __DIR__ . '/../prologue.html.php';

include __DIR__ . '/../header.html.php' ?>


        <div id="sub-header">
            <div>
                <h2 class="title"><?php echo $vars['title']; ?></h2>
            </div>

        </div>

        <div id="main">

            <div class="widget projects">
                <?php foreach ($vars['list'] as $project) {
                     echo View::get('project/widget/project.html.php', array(
                            'project' => $project
                            ));
                } ?>
            </div>

            <?php echo View::get('pagination.html.php', $vars); ?>
        </div>

        <?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
