<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

$pagedResults = new Paginated($vars['list'], 9, isset($_GET['page']) ? $_GET['page'] : 1);

$bodyClass = 'discover';

include __DIR__ . '/../prologue.html.php';

include __DIR__ . '/../header.html.php' ?>


        <div id="sub-header">
            <div>
                <!-- aqui pondremos avatar con nombre y lo que sea -->
                <h2 class="title"><?php echo $vars['title']; ?></h2>
            </div>
        </div>

        <div id="main">

            <div class="widget projects">
                <?php while ($project = $pagedResults->fetchPagedRow()) :
                        echo View::get('project/widget/project.html.php', array(
                            'project' => $project
                            ));
                endwhile; ?>
            </div>

            <ul id="pagination">
                <?php   $pagedResults->setLayout(new DoubleBarLayout());
                        echo $pagedResults->fetchPagedNavigation(); ?>
            </ul>

        </div>

        <?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
