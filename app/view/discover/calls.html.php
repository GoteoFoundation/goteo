<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Call,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

$calls     = Call::getActive(3); // convocatorias en modalidad 1; inscripcion de proyectos
$campaigns = Call::getActive(4); // convocatorias en modalidad 2; repartiendo capital riego
$success = Call::getActive(5); // convocatorias en modalidad 2; repartiendo capital riego

// en la página de cofinanciadores, paginación de 20 en 20
$pagedResults = new Paginated($vars['list'], 9, isset($_GET['page']) ? $_GET['page'] : 1);

$bodyClass = 'discover';

include __DIR__ . '/../prologue.html.php';

include __DIR__ . '/../header.html.php' ?>


        <div id="sub-header">
            <div>
                <h2 class="title"><?php echo $vars['title']; ?></h2>
            </div>
        </div>

        <div id="main">

            <div class="widget calls">

                <div class="title">
                    <div class="logo"><?php echo Text::get('home-calls-header'); ?></div>
                    <?php if (!empty($calls)) : ?>
                    <div class="call-count mod1">
                        <strong><?php echo count($calls) ?></strong>
                        <span>Convocatorias<br />abiertas</span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($campaigns)) : ?>
                    <div class="call-count mod2">
                        <strong><?php echo count($campaigns) ?></strong>
                        <span>Campañas<br />activas</span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($success)) : ?>
                    <div class="call-count mod3" style="margin-right: 0px;">
                        <strong><?php echo count($success) ?></strong>
                        <span>Convocatorias<br />exitosas</span>
                    </div>
                    <?php endif; ?>
                </div>

            <?php while ($call = $pagedResults->fetchPagedRow()) {
                echo View::get('call/widget/call.html.php', array('call' => $call));
            } ?>
            </div>

            <ul id="pagination">
                <?php   $pagedResults->setLayout(new DoubleBarLayout());
                        echo $pagedResults->fetchPagedNavigation(); ?>
            </ul>

        </div>

        <?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
