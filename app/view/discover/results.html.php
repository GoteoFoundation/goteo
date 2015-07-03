<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'discover';

include __DIR__ . '/../prologue.html.php';

include __DIR__ . '/../header.html.php' ?>

        <div id="sub-header">
            <div>
                <h2 class="title"><?php echo Text::get('discover-results-header'); ?></h2>
            </div>

        </div>

        <div id="main">
            <?php echo View::get('discover/searcher.html.php',
                                array('params'     => $vars['params'])); ?>

            <div class="widget projects">
                <?php if (!empty($vars['results'])) :
                    foreach ($vars['results'] as $result) :
                        echo View::get('project/widget/project.html.php', array(
                            'project' => $result
                        ));
                    endforeach;
                else :
                    echo Text::get('discover-results-empty');
                endif; ?>
            </div>

        </div>

        <?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
